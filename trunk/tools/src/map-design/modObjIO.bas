Attribute VB_Name = "modObjIO"
Option Explicit

' Resize models
Public Enum enmResizeModels
    szFixedSize = 0
    szBoxStretch = 1
    szNormalStretch = 2
End Enum

Public Enum enmCompileModes
    cmPHP = 1
    cmChunk = 2
    cmSerialized = 3
    cmASCII = 4
    cmSplit = 5
    smRepeat = -1
End Enum

' Resize model Parameters
Public Enum enmResizeModelParm

    ' szBoxStretch Parameters
    szCenterStretch = 1
    szHSideStretch = 2
    szVSideStretch = 3
    szBothSideStretch = 4
    
End Enum

Public Type COF_Head
    MIME As String * 4  ' Must be COB1 for current version
    Flags As Byte       ' Some info for the data structure
    ChunkCount As Byte  ' Number of data chunks included
End Type

Public Type COF_Head_PureSize
    TH As Byte  ' Top-row Height
    MH As Byte  ' Mid-row Height
    BH As Byte  ' Bottom-row Height
    LW As Byte  ' Left-col Width
    MW As Byte  ' Mid-col Width
    rW As Byte  ' Right-col Width
End Type

Public Type COF_Location_Info
    Elevation As Byte
    Attennuation As Byte
End Type

Public Type COF_Map_Info
    ' Real Map Tile Location
    MapX As Byte
    MapY As Byte
    MapZ As Byte
    
    ' Misc information
    Attennuation As Byte
    Material As Byte
    
    ' (For future use without editing the whole script)
    Reserved(1 To 8) As Byte
End Type

Public Type COF_Chunk_Data_Entry
    FileName As String * 24
End Type

Public Type COF_Chunk_Head
    Flags As Byte   ' Chunk structure and handling flags
    Width As Byte
    Height As Byte
    Layers As Byte
End Type

Public Type RAM_Chunk
    Width As Byte
    Height As Byte
    Layers As Byte
    FileNames() As String * 24
    MapInfo() As COF_Map_Info
End Type

' File Head flags
Public Const COF_HAS_PURESIZE = 1              ' The file includes a puresize header
Public Const COF_HAS_PREVIEW = 2               ' The file includes a preview bitmap

Public Const COF_MASK_RESIZE_MODEL = 24        ' The mask for the resizeModel Bits
Public Const COF_MASK_RESIZE_PARAM = 224       ' The mask for the resizeModel parameter Bits
Public Const COF_SHIFT_RESIZE_MODEL = 2 ^ 3    ' The shifting position for resize model
Public Const COF_SHIFT_RESIZE_PARAM = 2 ^ 5    ' The shifting position for resize model parameters


' Temporary cross-class data transfer variables
' Used for transparent object instancing and because custom-types
' cannot be public on classes :(
Public cPS As COF_Head_PureSize
Public cChunks() As RAM_Chunk
Public cChunkCount As Integer
Public cBlitMode As enmResizeModels
Public cBlitParam As enmResizeModelParm
Public cRandomizedCenter As Boolean
Public cObjectName As String

Public Function GetObjPreview(ByVal FileName As String) As StdPicture
    Dim f As Long, s As String, i As Integer, fHead As COF_Head, X, Y
    f = FreeFile
        
    Open FileName For Binary As #f
    
    ' Get Head
    Get #f, , fHead
    If Left(fHead.MIME, 3) <> "COB" Then Err.Raise &HFF01, "ObjectManager", "This is not a valid Compiled Object File!"
    If fHead.MIME <> "COB2" Then Err.Raise &HFF01, "ObjectManager", "This file version (v." & Right(fHead.MIME, 1) & ") is not currently supported!"
    
    ' Do we include a preview bitmap? If yes, Load it
    If (fHead.Flags And COF_HAS_PREVIEW) <> 0 Then
        On Error GoTo er
        Dim l As Long, Buf As String, tFile As String
        tFile = FixPath(TempPath()) & "preview.bmp"
        
        ' Data size
        Get #f, , l
        
        ' Get data buffer
        Buf = Space$(l)
        Get #f, , Buf
        
        ' We don't need the open file anymore
        Close #f
        
        ' Save to a file ..
        f = FreeFile
        Open tFile For Output As #f
        Close #f
        Open tFile For Binary As #f
        Put #f, , Buf
        Close #f
        
        ' .. and load it as object
        Set GetObjPreview = LoadPicture(tFile)
        
        ' We don't need the file any more
        Kill tFile
        Exit Function
    Else
        Close #f
        Set GetObjPreview = Nothing
    End If
er:
    Set GetObjPreview = Nothing
End Function
