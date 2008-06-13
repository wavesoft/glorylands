VERSION 5.00
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form Form1 
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Tileset Archiver v1.2 - By Wavesoft"
   ClientHeight    =   3315
   ClientLeft      =   45
   ClientTop       =   330
   ClientWidth     =   4710
   Icon            =   "Form1.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   3315
   ScaleWidth      =   4710
   StartUpPosition =   3  'Windows Default
   Begin VB.CommandButton Command2 
      Caption         =   "&Close"
      Height          =   375
      Left            =   3360
      TabIndex        =   5
      Top             =   2280
      Width           =   1215
   End
   Begin VB.CommandButton Command1 
      Caption         =   "&Create"
      Default         =   -1  'True
      Height          =   375
      Left            =   120
      TabIndex        =   3
      Top             =   2280
      Width           =   3135
   End
   Begin MSComctlLib.ProgressBar ProgressBar1 
      Align           =   2  'Align Bottom
      Height          =   270
      Left            =   0
      TabIndex        =   2
      Top             =   3045
      Width           =   4710
      _ExtentX        =   8308
      _ExtentY        =   476
      _Version        =   393216
      Appearance      =   1
   End
   Begin VB.ListBox List1 
      Height          =   1860
      Left            =   120
      Style           =   1  'Checkbox
      TabIndex        =   0
      Top             =   360
      Width           =   4455
   End
   Begin VB.Label Label2 
      Caption         =   " Select the tilesets you want to archive and press create"
      Height          =   255
      Left            =   0
      TabIndex        =   4
      Top             =   2760
      Width           =   4695
   End
   Begin VB.Label Label1 
      Caption         =   "Tilesets found on current directory:"
      Height          =   255
      Left            =   120
      TabIndex        =   1
      Top             =   120
      Width           =   4455
   End
End
Attribute VB_Name = "Form1"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
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

Private Sub Command1_Click()
    Dim i
    For i = 0 To List1.ListCount - 1
        If List1.Selected(i) Then
            CreateArchive List1.List(i)
        End If
    Next i
    MsgBox "Archiving completed successfully!", vbInformation
End Sub

Private Sub Command2_Click()
    End
End Sub

Public Function FixPath(ByVal Src As String) As String
    If Right(Src, 1) = "\" Then FixPath = Src Else FixPath = Src & "\"
End Function

Private Function DumpFile(ByVal Filename As String) As String
    Dim f As Long
    f = FreeFile
    Open Filename For Binary As #f
    DumpFile = Space$(LOF(f))
    Get #f, , DumpFile
    Close #f
End Function

Private Sub CreateArchive(ByVal BaseName As String)
    Dim FileNames As New Collection, l As Long, pX As Integer, pY As Integer
    Dim s As String, f() As String, i As Integer
    Dim szDataStart As String, Buf As String
    Dim fHead As MapFile_Head, iPos As Long, hPos As Long
    Dim fChunk As MapFile_DataChunk, cFile As String
    
    ' Load all the filenames we have
    Label2.Caption = " Searching files..."
    DoEvents
    s = Dir(FixPath(App.Path) & BaseName & "-*.gif")
    fHead.Length = 0
    Do While s <> ""
        ' Ensure the file is from the same base
        ' (For example, the above query will return both "something-X-X.gif" and "something-else-X-X.gif")
        If IsNumeric(Mid(Right(s, Len(s) - Len(BaseName) - 1), 1, 1)) Then
        
            ' The above check will ensure that there is a number on this position:
            '     the-real-name-0-23.gif
            '     |<-Basename->|^
        
            ' Stack file
            FileNames.Add s
            
            ' Split out the Y
            f = Split(Left(s, Len(s) - 4), "-")
            i = Val(f(UBound(f)))
            If i > fHead.Length Then fHead.Length = i
                
        End If
        
        ' Next file
        s = Dir
    Loop
    
    ' (Convert zero-based to 1-based)
    fHead.Length = fHead.Length + 1
    
    ' Find out where to put the data
    szDataStart = Len(fHead) + ((8 * fHead.Length) * Len(fChunk))
    
    ' Start file
    l = FreeFile
    Open FixPath(App.Path) & BaseName & ".ts" For Output As #l       ' Clean file
    Close #l
    Open FixPath(App.Path) & BaseName & ".ts" For Binary As #l
    
    ' Save Header
    fHead.TotalTiles = FileNames.Count
    fHead.MIME = "GLTS"
    Put #l, , fHead
    
    ' Start storing files
    iPos = szDataStart
    
    ProgressBar1.Max = FileNames.Count
    For i = 1 To FileNames.Count
        
        cFile = FileNames(i)
    
        ' Update UI
        Label2.Caption = " Storing " & cFile & ".."
        DoEvents
        
        ' Dump file into RAM
        Buf = DumpFile(FixPath(App.Path) & cFile)
        
        ' Identify file's X,Y coord
        cFile = Left(cFile, Len(cFile) - 4)
        f = Split(cFile, "-")
        pY = f(UBound(f))
        pX = f(UBound(f) - 1)
        
        ' Find out where to put the data location chunk
        hPos = Len(fHead) + (((pY * 8) + pX) * Len(fChunk))
    
        ' Prepare data chunk
        fChunk.Size = Len(Buf)
        fChunk.Position = iPos
        
        ' Store size chunk
        Put #l, hPos, fChunk
        
        ' Store the data chunk
        Put #l, iPos, Buf
    
        ' Prepare position of next chunk
        iPos = iPos + fChunk.Size
    
        ' Update UI
        ProgressBar1.Value = i
    Next i
    
    ' Close file
    Close #l
End Sub

Private Sub Form_Load()
    Dim s As String, i, f() As String
    s = Dir(FixPath(App.Path) & "*-0-0.gif")
    Do While s <> ""
    
        ' Get only the tileset name
        f = Split(s, "-")
        s = ""
        For i = 0 To UBound(f) - 2
            If s <> "" Then s = s & "-"
            s = s & f(i)
        Next i
        
        ' Store on list
        List1.AddItem s

        ' Next dir...
        s = Dir
    Loop
    SortBySize
End Sub

Private Sub SortBySize()
    Dim Names() As String
    Dim i, X, Sorted As Boolean
    
    Sorted = False
    Do While Not Sorted
        Sorted = True
        For X = 0 To List1.ListCount - 2
            If Len(List1.List(X)) < Len(List1.List(X + 1)) Then
                List1.AddItem List1.List(X), X + 2
                List1.RemoveItem X
                Sorted = False
            End If
        Next X
    Loop
End Sub
