VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "clsImgArchive"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

Private Type MapFile_Head
    MIME As String * 4
    Length As Long
    TotalTiles As Long
End Type

Private Type MapFile_DataChunk
    Position As Long
    Size As Long
End Type

Dim bPath As String

Public Function FileLength(ByVal File As String)
    Dim f As Long, fH As MapFile_Head
    f = FreeFile
    On Error GoTo er
    
    ' Open file
    Open bPath & File & ".ts" For Binary As #f
    
    ' Get Header & validate type
    Get #f, , fH
    If fH.MIME <> "GLTS" Then Exit Function
    
    ' Close the file
    Close #f
    
    ' Return the length
    FileLength = fH.Length
    Exit Function
er:
    Close #f
    FileLength = 0
End Function

Private Function GetChunk(ByVal File As String, ByVal ChunkX As Integer, ByVal ChunkY As Integer) As String
    Dim f As Long, fH As MapFile_Head, dC As MapFile_DataChunk, cOffset As Long
    f = FreeFile
    On Error GoTo er
    
    ' Open file
    Open bPath & File & ".ts" For Binary As #f
    
    ' Get Header & validate type
    Get #f, , fH
    If fH.MIME <> "GLTS" Then
        Close #f
        Exit Function
    End If
    
    ' If we are out of offset, return blank image
    If ChunkY >= fH.Length Then
        Close #f
        GetChunk = ""
        Exit Function
    End If
    
    ' Calculate offset
    cOffset = Len(fH) + ((ChunkY * 8) + ChunkX) * Len(dC)
    
    ' Get chunk info
    Get #f, cOffset, dC
    
    ' Load data if not blank
    Dim Buf As String
    If dC.Size = 0 Then
        Buf = ""
    Else
        Buf = Space$(dC.Size)
        Get #f, dC.Position, Buf
    End If
        
    ' Close file
    Close #f
    
    ' Return chunk
    GetChunk = Buf
    Exit Function
er:
    Close #f
    GetChunk = ""
End Function

Private Function ImageFromChunk(Buf As String) As StdPicture
    Dim f As Long
    On Error GoTo er
    f = FreeFile
    
    ' Dump data
    Open FixPath(App.Path) & "cache" For Output As #f
    Close #f
    Open FixPath(App.Path) & "cache" For Binary As #f
    Put #f, , Buf
    Close #f
    
    ' Load image
    Set ImageFromChunk = LoadPicture(FixPath(App.Path) & "cache")
    
    ' Successfull usage. Kill the file
    Kill FixPath(App.Path) & "cache"
    Exit Function
er:
    Close #f
    Set ImageFromChunk = Nothing
End Function

Public Function LoadImage(ByVal Filename As String) As StdPicture
    Dim f() As String, pX As Integer, pY As Integer, pFile As String, i, Buf As String
        
    ' Strip extension and format case
    f = Split(Filename, ".", 2)
    Filename = LCase(f(0))
    
    ' Analyze into parts
    f = Split(Filename, "-")
    pY = f(UBound(f))
    pX = f(UBound(f) - 1)
    For i = 0 To UBound(f) - 2
        If pFile <> "" Then pFile = pFile & "-"
        pFile = pFile & f(i)
    Next i
    
    ' Get image from chunk
    Buf = GetChunk(pFile, pX, pY)
    If Buf = "" Then
        Set LoadImage = Nothing
        Exit Function
    End If
    Set LoadImage = ImageFromChunk(Buf)
End Function

Private Sub Class_Initialize()
    bPath = FixPath(App.Path) & "tilesets\"
End Sub


