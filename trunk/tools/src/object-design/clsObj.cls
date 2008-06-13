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

Private Type COF_Head
    MIME As String * 4  ' Must be COB1 for current version
    flags As Byte       ' Some info for the data structure
    ChunkCount As Byte  ' Number of data chunks included
End Type

Private Type COF_Chunk_Array
    Entries() As COF_Chunk_Data_Entry
    MapInfo() As COF_Map_Info
End Type

' File Head flags
Private Const COF_HAS_PURESIZE = 1              ' The file includes a puresize header
Private Const COF_HAS_PREVIEW = 2               ' The file includes a preview bitmap

Private Const COF_MASK_RESIZE_MODEL = 24        ' The mask for the resizeModel Bits
Private Const COF_MASK_RESIZE_PARAM = 224       ' The mask for the resizeModel parameter Bits
Private Const COF_SHIFT_RESIZE_MODEL = 2 ^ 3    ' The shifting position for resize model
Private Const COF_SHIFT_RESIZE_PARAM = 2 ^ 5    ' The shifting position for resize model parameters

' Chunk head flags

Public Preview As StdPicture

Dim fHead As COF_Head
Dim fHasPureHead As Boolean
Dim fPureHead As COF_Head_PureSize
Dim fChunks() As COF_Chunk_Array
Dim fChunkInfo() As COF_Chunk_Head
Public ResizeModel As Byte
Public ResizeModelParm As Byte

Dim hFileName As String

Private Sub SavePreviewToFile(ByVal hFile As Long)
    On Error GoTo er
    If Preview Is Nothing Then Exit Sub
    
    Dim l As Long, Buf As String, tFile As String
    tFile = FixPath(App.Path) & "preview.bmp"
    
    ' Save image
    SavePicture Preview, tFile
    
    ' Dump file
    Dim f As Long
    f = FreeFile
    Open tFile For Binary As #f
    l = LOF(f)
    Buf = Space$(l)
    Get #f, , Buf
    Close #f
    
    ' Save data to file
    ' (Size)
    Put #hFile, , l
    
    ' (Data Chunk)
    Put #hFile, , Buf
er:

    On Error Resume Next
    ' We don't need the file any more
    Kill tFile
End Sub

Private Sub LoadPreviewFromFile(ByVal hFile As Long)
    On Error GoTo er
    Dim l As Long, Buf As String, tFile As String
    tFile = FixPath(App.Path) & "preview.bmp"
    
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
    On Error Resume Next
    Kill tFile
    Set Preview = Nothing
End Sub

Public Sub SaveFile(Optional Filename As String = "")
    Dim f As Long, s As String, i As Integer, cHead As COF_Chunk_Head, x, y, l
    f = FreeFile
    
    ' Store filename
    If Filename <> "" Then hFileName = Filename
    If hFileName = "" Then Exit Sub
    
    ' Erase file
    Open hFileName For Output As #f
    Close #f
    
    ' Start writting
    Open hFileName For Binary As #f
    
    ' Prepare head
    fHead.MIME = "COB2"
    fHead.ChunkCount = 9
    fHead.flags = COF_HAS_PURESIZE
    
    ' Check preview
    If Not Preview Is Nothing Then fHead.flags = fHead.flags Or COF_HAS_PREVIEW
    
    ' Merge resize model & parameter flags
    l = (ResizeModel * COF_SHIFT_RESIZE_MODEL) And COF_MASK_RESIZE_MODEL
    fHead.flags = fHead.flags Or l
    l = (ResizeModelParm * COF_SHIFT_RESIZE_PARAM) And COF_MASK_RESIZE_PARAM
    fHead.flags = fHead.flags Or l

    ' Save Head
    Put #f, , fHead
    
    ' Not implemented -----
    If Not Preview Is Nothing Then
        SavePreviewToFile f
    End If
    
    ' Save purehead
    CreatePureSize
    Put #f, , fPureHead
        
    ' Create our chunk instances
    ReDim fChunks(fHead.ChunkCount - 1)
    ReDim fChunkInfo(fHead.ChunkCount - 1)
    
    ' Load the chunks
    For i = 0 To 8
        ' Convert our chunk
        ConvertChunk Grids(i), fChunks(i), fChunkInfo(i)
    
        ' Save chunk info
        Put #f, , fChunkInfo(i)
        
        ' Save the chunk
        For x = 0 To fChunkInfo(i).Width - 1
            For y = 0 To fChunkInfo(i).Height - 1
                For l = 0 To fChunkInfo(i).Layers - 1
                    Put #f, , fChunks(i).Entries(x, y, l).Filename
                Next l
                Put #f, , fChunks(i).MapInfo(x, y)
            Next y
        Next x
    Next i
    
    ' File Input operations completed. Close the file
    Close #f
End Sub

Private Sub ConvertChunk(inGrid As TileGrid, outGrid As COF_Chunk_Array, outInfo As COF_Chunk_Head)
    Dim iX, iY, iL
    outInfo.Width = inGrid.ActualW
    outInfo.Height = inGrid.ActualH
    outInfo.Layers = inGrid.ActualL
    
    If (inGrid.ActualW = 0) Or (inGrid.ActualH = 0) Or (inGrid.ActualL = 0) Then
        ReDim outGrid.Entries(0, 0, 0)
        ReDim outGrid.MapInfo(0, 0)
        Exit Sub
    End If
    
    ReDim outGrid.Entries(inGrid.ActualW - 1, inGrid.ActualH - 1, inGrid.ActualL - 1)
    ReDim outGrid.MapInfo(inGrid.ActualW - 1, inGrid.ActualH - 1)
    
    For iX = 0 To inGrid.ActualW - 1
        For iY = 0 To inGrid.ActualH - 1
            For iL = 0 To inGrid.ActualL - 1
                outGrid.Entries(iX, iY, iL).Filename = inGrid.Grid(iL, iX, iY)
            Next iL
            outGrid.MapInfo(iX, iY) = inGrid.MapInfo(iX, iY)
        Next iY
    Next iX
End Sub

Private Sub CreatePureSize()
    Dim i
    Dim MaxTopH As Integer
    Dim MaxBotH As Integer
    Dim MaxMidH As Integer
    Dim MaxLeftW As Integer
    Dim MaxRightW As Integer
    Dim MaxMidW As Integer
    
    ' 1) Calculate the maximum height/widths
    For i = 0 To 2  ' 0,1,2
        If Grids(i).ActualH > MaxTopH Then MaxTopH = Grids(i).ActualH
    Next i
    For i = 3 To 5  ' 3,4,5
        If Grids(i).ActualH > MaxMidH Then MaxMidH = Grids(i).ActualH
    Next i
    For i = 6 To 8  ' 6,7,8
        If Grids(i).ActualH > MaxBotH Then MaxBotH = Grids(i).ActualH
    Next i
    For i = 0 To 6 Step 3  ' 0,3,6
        If Grids(i).ActualW > MaxLeftW Then MaxLeftW = Grids(i).ActualW
    Next i
    For i = 1 To 7 Step 3  ' 1,4,7
        If Grids(i).ActualW > MaxMidW Then MaxMidW = Grids(i).ActualW
    Next i
    For i = 2 To 8 Step 3  ' 2,5,8
        If Grids(i).ActualW > MaxRightW Then MaxRightW = Grids(i).ActualW
    Next i
    
    fPureHead.BH = MaxBotH
    fPureHead.LW = MaxLeftW
    fPureHead.MH = MaxMidH
    
    fPureHead.mW = MaxMidW
    fPureHead.RW = MaxRightW
    fPureHead.TH = MaxTopH
End Sub
