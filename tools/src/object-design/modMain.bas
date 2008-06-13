Attribute VB_Name = "modMain"
Option Explicit

' Resize models
Public Enum enmResizeModels
    szFixedSize = 0
    szBoxStretch = 1
    szNormalStretch = 2
End Enum

' Resize model Parameters
Public Enum enmResizeModelParm

    ' szBoxStretch Parameters
    szCenterStretch = 1
    szHSideStretch = 2
    szVSideStretch = 3
    szBothSideStretch = 4
    
End Enum

Public Enum PenMode
    pmErase = 0
    pmInsert = 1
    pmShift_1Up = 2
    pmShift_5Up = 3
    pmShift_1Down = 4
    pmShift_5Down = 5
    pmInsertAutoLayer = 6
End Enum

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

Public Type TileGrid
    ActualW As Integer
    ActualH As Integer
    ActualL As Byte
    Grid(3, 15, 11) As String * 24
    RAMGrid(3, 15, 11) As StdPicture
    MapInfo(15, 11) As COF_Map_Info
End Type

Public Type ObjectHead
    IDF As String * 4
    objectName As String * 24
End Type

Public Type COF_Head_PureSize
    TH As Byte  ' Top-row Height
    MH As Byte  ' Mid-row Height
    BH As Byte  ' Bottom-row Height
    LW As Byte  ' Left-col Width
    mW As Byte  ' Mid-col Width
    RW As Byte  ' Right-col Width
End Type

Public Type COF_Location_Info
    Elevation As Byte
    Attennuation As Byte
End Type

Public Type COF_Chunk_Data_Entry
    Filename As String * 24
End Type

Public Type COF_Chunk_Head
    Flags As Byte   ' Chunk structure and handling flags
    Width As Byte
    Height As Byte
    Layers As Byte
End Type

Public BasePath As String
Public Grids(8) As TileGrid
Public imgCache As New clsImgLib
Public imgStorage As New clsImgArchive

Public Function FixPath(ByVal Src As String) As String
    If Right(Src, 1) = "\" Then FixPath = Src Else FixPath = Src & "\"
End Function

Sub main()
    BasePath = FixPath(App.Path) & "\tilesets\"
    frmMain.Show
End Sub
