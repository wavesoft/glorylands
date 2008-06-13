VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "clsObjIO"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

' =========================
'    Object File Format
' =========================
'
'  Requires
' ~-~-~-~-~-
'
' 1) [OPTIMIZED] Initial gameobject column sizes for quick resize calculation
'
'  <--- LW --->   <--- MW --->  <--- RW --->
' +-------------+-------------+-------------+  -
' |             |             |             |  |
' |             |             |             | TH
' |             |             |             |  |
' +-------------+-------------+-------------+  -
' |             |             |             |  |
' |             |             |             | MH
' |             |             |             |  |
' +-------------+-------------+-------------+  -
' |             |             |             |  |
' |             |             |             |  BH
' |             |             |             |  |
' +-------------+-------------+-------------+  -
'
' 2) [EXTENSIBILITY-OPTIMIZATION] Pre-compiled design algorithms and file structure
'
'  {1} Fixed-Sized Grid       : Contains ONLY the static grid in array in (layer,w,h)@str24 format
'
'  {2} Stretched Mode A,B,C,D : Contains 9 data blocks and their default sizes
'
'
'  File Flags:
'
'  .......1  :  Contain PureSize Header
'  ......1.  :  Contain Preview Bitmap
'  .....1..  :  [Future Plan] Extended Header
'  ...xx...  :  Resize model
'  xxx.....  :  Resize model parameter
'
'  Chunk Flags:
'
'  .......1  :  Randomized data
'  ......1.  :  Include layers on randomization
'  ........  :  (Unused)
'
'  Compiled Object File Format
' ~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-
'           +-------------+
' Head:     |   "COF1"    | (MIME Type identifier)
'           |    Flags    | (File flags)
'           |Chunk Count  | (Number of chunks in file)
'           .             .
'           .             .
'           | Chunk Flags | (Chunk-specific flags)
'           |ChunkW,ChunkH| (Chunk dimensions)
'           |Tile,Tile,.. |
'           |Tile,Tile,.. |  Data Area
'           |Tile,Tile,.. |
'           .             .
'           .             .

Private Type COF_Chunk_Array
    Entries() As COF_Chunk_Data_Entry
    MapInfo() As COF_Map_Info
End Type

' Chunk head flags

Public Preview As StdPicture

Dim fHead As COF_Head
Dim fHasPureHead As Boolean
Dim fPureHead As COF_Head_PureSize
Dim fChunks() As COF_Chunk_Array
Dim fChunkInfo() As COF_Chunk_Head
Dim fResizeModel As Byte
Dim fResizeModelParm As Byte
Dim hLoaded As Boolean

Dim hFileName As String

Private Sub LoadPreviewFromFile(ByVal hFile As Long)
    On Error GoTo er
    Dim l As Long, Buf As String, tFile As String
    tFile = FixPath(TempPath()) & "preview.bmp"
    
    ' Data size
    Get #hFile, , l
    
    ' Get data buffer
    Buf = Space$(l)
    Get #hFile, , Buf
    
    ' Save to a file ..
    l = FreeFile
    Open tFile For Output As #l
    Close #l
    Open tFile For Binary As #l
    Put #l, , Buf
    Close #l
    
    ' .. and load it as object
    Set Preview = LoadPicture(tFile)
    
    ' We don't need the file any more
    Kill tFile
    Exit Sub
er:
    Set Preview = Nothing
End Sub

Public Sub LoadFile(Optional FileName As String = "")
    Dim f As Long, s As String, i As Integer, cHead As COF_Chunk_Head, X, Y, l
    f = FreeFile
    hLoaded = False
    
    ' Store filename
    If FileName <> "" Then hFileName = FileName
    If hFileName = "" Then Exit Sub
    
    Open hFileName For Binary As #f
    
    ' Get Head
    Get #f, , fHead
    If Left(fHead.MIME, 3) <> "COB" Then Err.Raise &HFF01, "ObjectManager", "This is not a valid Compiled Object File!"
    If fHead.MIME <> "COB2" Then Err.Raise &HFF01, "ObjectManager", "This file version (v." & Right(fHead.MIME, 1) & ") is not currently supported!"
    
    ' Do we include a preview bitmap? If yes, Load it
    If (fHead.Flags And COF_HAS_PREVIEW) <> 0 Then
        LoadPreviewFromFile f
    Else
        Set Preview = Nothing
    End If
    
    ' Do we include a PureSize header? If yes, load it
    fHasPureHead = (fHead.Flags And COF_HAS_PURESIZE) <> 0
    If fHasPureHead Then
        Get #f, , fPureHead
    End If
    
    ' Store remaining info on variables
    i = (fHead.Flags And COF_MASK_RESIZE_MODEL) / COF_SHIFT_RESIZE_MODEL
    fResizeModel = i
    i = (fHead.Flags And COF_MASK_RESIZE_PARAM) / COF_SHIFT_RESIZE_PARAM
    fResizeModelParm = i
    
    ' Create our chunk instances
    ReDim fChunks(fHead.ChunkCount - 1)
    ReDim fChunkInfo(fHead.ChunkCount - 1)
    
    ' Load the chunks
    For i = 0 To fHead.ChunkCount - 1
        ' Load chunk info
        Get #f, , cHead
        fChunkInfo(i) = cHead
        
        ' Resize the chunk array
        If (cHead.Width = 0) Or (cHead.Height = 0) Or (cHead.Layers = 0) Then
            ReDim fChunks(i).Entries(0, 0, 0)
            ReDim fChunks(i).MapInfo(0, 0)
        Else
            ReDim fChunks(i).Entries(cHead.Width - 1, cHead.Height - 1, cHead.Layers - 1)
            ReDim fChunks(i).MapInfo(cHead.Width - 1, cHead.Height - 1)
        End If
        
        ' Load the chunk
        For X = 0 To fChunkInfo(i).Width - 1
            For Y = 0 To fChunkInfo(i).Height - 1
                For l = 0 To fChunkInfo(i).Layers - 1
                    Get #f, , fChunks(i).Entries(X, Y, l).FileName
                Next l
                Get #f, , fChunks(i).MapInfo(X, Y)
            Next Y
        Next X
    Next i
    
    ' File Input operations completed. Close the file
    Close #f
    hLoaded = True
End Sub

Public Function CreateInstance() As clsObjInstance
    Dim o As clsObjInstance
    Dim i, X, Y, l
    
    'PureSize = cPS
    'Chunks = cChunks
    'RandomizeCenter = cRandomizedCenter
    'PenMode = cBlitMode
    
    ' Public assigned variables
    cPS = fPureHead
    cChunkCount = UBound(fChunks) + 1
    ReDim cChunks(cChunkCount - 1)
    For i = 0 To UBound(fChunks)
        
        ' Store info
        cChunks(i).Height = fChunkInfo(i).Height
        cChunks(i).Width = fChunkInfo(i).Width
        cChunks(i).Layers = fChunkInfo(i).Layers
        
        ' Redim Arrays if not null
        If (fChunkInfo(i).Width = 0) Or (fChunkInfo(i).Height = 0) Or (fChunkInfo(i).Layers = 0) Then
            ReDim cChunks(i).FileNames(0, 0, 0)
            ReDim cChunks(i).MapInfo(0, 0)
        Else
            ReDim cChunks(i).FileNames(fChunkInfo(i).Width - 1, fChunkInfo(i).Height - 1, fChunkInfo(i).Layers - 1)
            ReDim cChunks(i).MapInfo(fChunkInfo(i).Width - 1, fChunkInfo(i).Height - 1)
        End If
        
        ' Assign values
        For X = 0 To fChunkInfo(i).Width - 1
            For Y = 0 To fChunkInfo(i).Height - 1
                For l = 0 To fChunkInfo(i).Layers - 1
                    cChunks(i).FileNames(X, Y, l) = fChunks(i).Entries(X, Y, l).FileName
                Next l
                cChunks(i).MapInfo(X, Y) = fChunks(i).MapInfo(X, Y)
            Next Y
        Next X
        
    Next i
    
    ' Save blit modes
    cBlitMode = fResizeModel
    cBlitParam = fResizeModelParm
    
    ' (Not implemented yet)
    cRandomizedCenter = False
    
    ' Create new instalce
    Set o = New clsObjInstance
    
    Dim f() As String
    f = Split(hFileName, "\")
    o.InstanceOf = f(UBound(f))
    
    Set CreateInstance = o
End Function

Public Property Get ResizeModel() As enmResizeModels
    ResizeModel = fResizeModel
End Property

Public Property Get ResizeParameters() As enmResizeModelParm
    ResizeParameters = fResizeModelParm
End Property

Public Property Get ChunkWidth(ByVal ChunkID As Integer) As Integer
    On Error GoTo er
    ChunkWidth = fChunkInfo(ChunkID).Width
er:
End Property

Public Property Get ChunkHeight(ByVal ChunkID As Integer) As Integer
    On Error GoTo er
    ChunkHeight = fChunkInfo(ChunkID).Height
er:
End Property

Public Property Get ObjectWidth() As Integer
    ObjectWidth = fPureHead.LW + fPureHead.MW + fPureHead.rW
End Property

Public Property Get ObjectHeight() As Integer
    ObjectHeight = fPureHead.TH + fPureHead.MH + fPureHead.BH
End Property

Public Property Get Chunks() As Integer
    Chunks = UBound(fChunks) + 1
End Property

Private Sub Class_Initialize()
    hLoaded = False
End Sub

Public Property Get Loaded() As Boolean
    Loaded = hLoaded
End Property
