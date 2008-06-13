Attribute VB_Name = "modMain"
Option Explicit

Private Declare Function GetTempPath Lib "kernel32" Alias "GetTempPathA" (ByVal nBufferLength As Long, ByVal lpBuffer As String) As Long

' Blit modes we support
Public Enum BlitMode
    bltNoStretch = 1
    bltCenterStretch = 2
    bltHSideStretch = 3
    bltVSideStretch = 4
    bltBothSideStretch = 5
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

Public Type RGN
    Left As Integer
    Top As Integer
    Width As Integer
    Height As Integer
End Type

Public Type RGNex
    Left As Integer
    Top As Integer
    Width As Integer
    Height As Integer
    Right As Integer
    Bottom As Integer
End Type

Public lastCMP_Mode As Byte

'Public BasePath As String
Public ImgLib As New clsImgLib
Public ImgArc As New clsImgArchive

Public Function FixPath(ByVal Src As String) As String
    If Right(Src, 1) = "\" Then FixPath = Src Else FixPath = Src & "\"
End Function

Public Function TempPath() As String
    Dim Buf As String, f() As String
    Buf = Space$(256)
    GetTempPath 256, Buf
    f = Split(Buf, vbNullChar)
    TempPath = Trim(f(0))
End Function

