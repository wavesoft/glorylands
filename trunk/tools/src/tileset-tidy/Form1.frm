VERSION 5.00
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form Form1 
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Tileset Tidy"
   ClientHeight    =   6345
   ClientLeft      =   45
   ClientTop       =   330
   ClientWidth     =   9555
   ControlBox      =   0   'False
   Icon            =   "Form1.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   6345
   ScaleWidth      =   9555
   StartUpPosition =   2  'CenterScreen
   Begin VB.CommandButton Command11 
      Caption         =   "E&xit"
      Height          =   855
      Left            =   7320
      Picture         =   "Form1.frx":212A
      Style           =   1  'Graphical
      TabIndex        =   35
      Top             =   5400
      Width           =   2175
   End
   Begin VB.CommandButton Command10 
      Caption         =   "Build"
      Height          =   855
      Left            =   4320
      Picture         =   "Form1.frx":23B1
      Style           =   1  'Graphical
      TabIndex        =   34
      Top             =   5400
      Width           =   2895
   End
   Begin VB.Frame Frame3 
      Caption         =   "Build Process"
      Height          =   1815
      Left            =   4320
      TabIndex        =   29
      Top             =   3480
      Width           =   5175
      Begin MSComctlLib.ProgressBar ProgressBar1 
         Height          =   255
         Left            =   120
         TabIndex        =   31
         Top             =   1000
         Width           =   4935
         _ExtentX        =   8705
         _ExtentY        =   450
         _Version        =   393216
         Appearance      =   1
      End
      Begin VB.Label Label11 
         Caption         =   "chateu-englise-0-0.gif"
         BeginProperty Font 
            Name            =   "MS Sans Serif"
            Size            =   8.25
            Charset         =   161
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   480
         TabIndex        =   33
         Top             =   1440
         Width           =   3255
      End
      Begin VB.Label Label10 
         Caption         =   "File:"
         Height          =   255
         Left            =   120
         TabIndex        =   32
         Top             =   1440
         Width           =   375
      End
      Begin VB.Line Line8 
         BorderColor     =   &H80000014&
         X1              =   120
         X2              =   5040
         Y1              =   1320
         Y2              =   1320
      End
      Begin VB.Line Line7 
         BorderColor     =   &H80000010&
         BorderWidth     =   2
         X1              =   120
         X2              =   5040
         Y1              =   1320
         Y2              =   1320
      End
      Begin VB.Line Line6 
         BorderColor     =   &H80000014&
         X1              =   120
         X2              =   5040
         Y1              =   960
         Y2              =   960
      End
      Begin VB.Line Line5 
         BorderColor     =   &H80000010&
         BorderWidth     =   2
         X1              =   120
         X2              =   5040
         Y1              =   960
         Y2              =   960
      End
      Begin VB.Label Label9 
         Caption         =   "Please prepare your packages and hit build to start packing..."
         Height          =   255
         Left            =   720
         TabIndex        =   30
         Top             =   480
         Width           =   4335
      End
      Begin VB.Image Image1 
         Height          =   480
         Left            =   120
         Picture         =   "Form1.frx":2636
         Top             =   360
         Width           =   480
      End
   End
   Begin VB.Frame Frame2 
      Caption         =   "Package Management"
      Height          =   6135
      Left            =   120
      TabIndex        =   9
      Top             =   120
      Width           =   4095
      Begin VB.TextBox Text6 
         Height          =   285
         Left            =   3480
         TabIndex        =   37
         Text            =   "1"
         Top             =   3360
         Width           =   495
      End
      Begin VB.CheckBox Check3 
         Caption         =   "Delete source files"
         Height          =   255
         Left            =   120
         TabIndex        =   36
         Top             =   5760
         Width           =   3855
      End
      Begin VB.CheckBox Check2 
         Caption         =   "Use raw tileset packing instead of folder-based"
         Height          =   255
         Left            =   120
         TabIndex        =   28
         Top             =   5520
         Width           =   3855
      End
      Begin VB.CheckBox Check1 
         Caption         =   "Create folder for each package"
         Height          =   255
         Left            =   120
         TabIndex        =   27
         Top             =   5280
         Value           =   1  'Checked
         Width           =   3855
      End
      Begin VB.TextBox Text5 
         Height          =   285
         Left            =   960
         TabIndex        =   26
         Top             =   4800
         Width           =   3015
      End
      Begin VB.TextBox Text4 
         Height          =   285
         Left            =   960
         TabIndex        =   24
         Text            =   "(CC) Copyleft by various sources"
         Top             =   4440
         Width           =   3015
      End
      Begin VB.TextBox Text3 
         Height          =   285
         Left            =   720
         TabIndex        =   22
         Text            =   "John Haralampidis - Wavesoft"
         Top             =   4080
         Width           =   3255
      End
      Begin VB.TextBox Text2 
         Height          =   285
         Left            =   720
         TabIndex        =   20
         Text            =   "Tileset graphics. Includes: %L"
         Top             =   3720
         Width           =   3255
      End
      Begin VB.TextBox Text1 
         Height          =   285
         Left            =   720
         TabIndex        =   18
         Text            =   "Tileset - %N"
         Top             =   3360
         Width           =   1935
      End
      Begin VB.CommandButton Command9 
         Caption         =   "Reset All"
         Height          =   375
         Left            =   2400
         TabIndex        =   16
         Top             =   2760
         Width           =   1575
      End
      Begin VB.CommandButton Command8 
         Caption         =   "Reset Selected"
         Height          =   375
         Left            =   2400
         TabIndex        =   15
         Top             =   2400
         Width           =   1575
      End
      Begin VB.CommandButton Command6 
         Caption         =   "Remove"
         Height          =   375
         Left            =   2400
         TabIndex        =   11
         Top             =   840
         Width           =   1575
      End
      Begin VB.ListBox List3 
         Height          =   2595
         Left            =   120
         TabIndex        =   13
         Top             =   480
         Width           =   2175
      End
      Begin VB.CommandButton Command5 
         Caption         =   "Add"
         Height          =   375
         Left            =   2400
         TabIndex        =   12
         Top             =   480
         Width           =   1575
      End
      Begin VB.CommandButton Command7 
         Caption         =   "Auto Assign All"
         Height          =   495
         Left            =   2400
         TabIndex        =   10
         Top             =   1560
         Width           =   1575
      End
      Begin VB.Label Label12 
         Caption         =   "Version :"
         Height          =   255
         Left            =   2760
         TabIndex        =   38
         Top             =   3375
         Width           =   735
      End
      Begin VB.Line Line3 
         BorderColor     =   &H80000014&
         X1              =   120
         X2              =   3960
         Y1              =   5160
         Y2              =   5160
      End
      Begin VB.Label Label8 
         Caption         =   "Website :"
         Height          =   255
         Left            =   120
         TabIndex        =   25
         Top             =   4830
         Width           =   975
      End
      Begin VB.Label Label7 
         Caption         =   "Copyright :"
         Height          =   255
         Left            =   120
         TabIndex        =   23
         Top             =   4470
         Width           =   975
      End
      Begin VB.Label Label6 
         Caption         =   "Author :"
         Height          =   255
         Left            =   120
         TabIndex        =   21
         Top             =   4095
         Width           =   975
      End
      Begin VB.Label Label5 
         Caption         =   "Desc :"
         Height          =   255
         Left            =   120
         TabIndex        =   19
         Top             =   3735
         Width           =   975
      End
      Begin VB.Label Label4 
         Caption         =   "Name:"
         Height          =   255
         Left            =   120
         TabIndex        =   17
         Top             =   3380
         Width           =   975
      End
      Begin VB.Line Line2 
         BorderColor     =   &H80000014&
         X1              =   120
         X2              =   3960
         Y1              =   3240
         Y2              =   3240
      End
      Begin VB.Line Line1 
         BorderColor     =   &H80000010&
         BorderWidth     =   2
         X1              =   120
         X2              =   3960
         Y1              =   3240
         Y2              =   3240
      End
      Begin VB.Label Label3 
         Caption         =   "Packages :"
         Height          =   255
         Left            =   120
         TabIndex        =   14
         Top             =   240
         Width           =   1935
      End
      Begin VB.Line Line4 
         BorderColor     =   &H80000010&
         BorderWidth     =   2
         X1              =   120
         X2              =   3960
         Y1              =   5160
         Y2              =   5160
      End
   End
   Begin VB.Frame Frame1 
      Caption         =   "Package Assignment"
      Height          =   3255
      Left            =   4320
      TabIndex        =   0
      Top             =   120
      Width           =   5175
      Begin VB.CommandButton Command4 
         Caption         =   "<"
         Height          =   375
         Left            =   2400
         TabIndex        =   8
         Top             =   2160
         Width           =   375
      End
      Begin VB.CommandButton Command3 
         Caption         =   "<<"
         Height          =   375
         Left            =   2400
         TabIndex        =   7
         Top             =   1680
         Width           =   375
      End
      Begin VB.CommandButton Command2 
         Caption         =   ">>"
         Height          =   375
         Left            =   2400
         TabIndex        =   6
         Top             =   1200
         Width           =   375
      End
      Begin VB.CommandButton Command1 
         Caption         =   ">"
         Height          =   375
         Left            =   2400
         TabIndex        =   5
         Top             =   720
         Width           =   375
      End
      Begin VB.ListBox List2 
         Height          =   2595
         Left            =   2880
         MultiSelect     =   2  'Extended
         TabIndex        =   4
         Top             =   480
         Width           =   2175
      End
      Begin VB.ListBox List1 
         Height          =   2595
         Left            =   120
         MultiSelect     =   2  'Extended
         TabIndex        =   2
         Top             =   480
         Width           =   2175
      End
      Begin VB.Label Label2 
         Caption         =   "Tilesets in package:"
         Height          =   255
         Left            =   2880
         TabIndex        =   3
         Top             =   240
         Width           =   1695
      End
      Begin VB.Label Label1 
         Caption         =   "Tilesets found:"
         Height          =   255
         Left            =   120
         TabIndex        =   1
         Top             =   240
         Width           =   1335
      End
   End
End
Attribute VB_Name = "Form1"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
Private Declare Function GetTickCount Lib "kernel32" () As Long
Private Declare Function GetCursorPos Lib "user32" (lpPoint As POINTAPI) As Long
Private Type POINTAPI
        x As Long
        y As Long
End Type

Dim RealNames() As String
Dim PackageFiles() As String
Dim Packages As Integer

Private Sub Command1_Click()
    If List1.ListIndex < 0 Then
        If List1.ListCount > 0 Then
            List1.ListIndex = 0
        Else
            Exit Sub
        End If
    End If
    If List3.ListIndex < 0 Then
        If List3.ListCount > 0 Then
            List3.ListIndex = 0
        Else
            Exit Sub
        End If
    End If
    Dim i
    For i = List1.ListCount - 1 To 0 Step -1
        If List1.Selected(i) Then
            PackageFiles(List3.ListIndex) = PackageFiles(List3.ListIndex) & List1.List(i) & ";"
            List2.AddItem List1.List(i)
            List1.RemoveItem i
        End If
    Next i
End Sub

Private Sub Command10_Click()
    Dim i, s As String, DestDir As String, FileCache As String, f As Long
    ProgressBar1.Max = List3.ListCount
    For i = 0 To List3.ListCount - 1
        s = List3.List(i)
    
        ' Make folder if requested
        If Check1.Value = 1 Then
            If Dir(s, vbDirectory) = "" Then MkDir s
        End If
        
        ' In stacking mode, copy all the files into the root dir
        If Check2.Value = 1 Then
            DestDir = s & "\"
        Else
            ' Else, make a subdir named 'tiles' and put them there
            If Dir(s & "\tiles", vbDirectory) = "" Then MkDir s & "\tiles"
            DestDir = s & "\tiles\"
        End If
    
        ' Start copying files
        Dim tC As String
        Dim j
        Dim w() As String
        Dim q
        Label9.Caption = "Copying files..."
        DoEvents
        
        w = Split(PackageFiles(i), ";")
        For Each q In w
            If q <> "" Then
                tC = CopyFiles("", q, DestDir)
                FileCache = FileCache & tC
            End If
        Next q
        
        ' Prepare XML
        Dim DescFiles As String
        Label9.Caption = "Generating config file..."
        DoEvents
        
        DescFiles = Replace(PackageFiles(i), ";", ",")
        DescFiles = Left(DescFiles, Len(DescFiles) - 1)
        
        f = FreeFile
        Open s & "\package.xml" For Output As #f
        Print #f, "<?xml version=""1.0"" encoding=""iso-8859-7""?>"
        Print #f, "<package>"
        Print #f, "    <guid>" & MakeGuid() & "</guid>"
        Print #f, "    <name>" & Replace(Text1.Text, "%N", s) & "</name>"
        Print #f, "    <version>" & Text6.Text & "</version>"
        Print #f, "    <description>" & Replace(Text2.Text, "%L", DescFiles) & "</description>"
        Print #f, "    <author>" & Text3.Text & "</author>"
        Print #f, "    <copyright>" & Text4.Text & "</copyright>"
        Print #f, "    <website>" & Text5.Text & "</website>"
        Print #f, "    <files>"
        If (Check2.Value = 1) Then
            Print #f, FileCache
        Else
            Print #f, "        <file type=""IMAGE.TILES"" subdir=""/"" recurse=""yes"">tiles</file>"
        End If
        Print #f, "    </files>"
        Print #f, "</package>"
        Close #f
                
        ProgressBar1.Value = i + 1
        DoEvents
    Next i
End Sub

' Generate a random number
Private Function MakeGuid() As String
    Const CHARS = "0123456789abcdef"
    Dim s As String, sz As Byte
    sz = Len(CHARS) - 1
    '32
    Randomize
    Randomize Hour(Date) Xor Round(Rnd * &HFFFF)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    Randomize Minute(Date) Xor Round(Rnd * &HFFFF)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    Randomize Second(Date) Xor Round(Rnd * &HFFFF)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    Randomize Year(Date) Xor Round(Rnd * &HFFFF)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    Randomize Month(Date) Xor Round(Rnd * &HFFFF)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    Randomize Day(Date) Xor Round(Rnd * &HFFFF)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    Randomize GetTickCount Xor Round(Rnd * &HFFFF)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    Dim PA As POINTAPI
    GetCursorPos PA
    Randomize PA.x Xor PA.y Xor Round(Rnd * &HFFFF)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    s = s & Mid(CHARS, Round(Rnd * sz) + 1, 1)
    MakeGuid = s
End Function

' Copy files and return a list containing all the files being transfered in XML mode
Private Function CopyFiles(ByVal DirPath As String, ByVal SearchName As String, ByVal DestDir As String) As String
    Dim s As String, Cache As String
    s = Dir(DirPath & SearchName & "*.gif", vbNormal)
    Cache = ""
    Do While s <> ""
        FileCopy DirPath & s, DestDir & s
        Label11.Caption = s
        Cache = Cache & "        <file type=""IMAGE.TILES"" subdir=""/"">" & DirPath & s & "</file>" & vbCrLf
        If Check3.Value = 1 Then
            'Debug.Print "Kill"; DirPath & s
            Kill DirPath & s
        End If
        DoEvents
        s = Dir
    Loop
    CopyFiles = Cache
End Function

Private Sub Command11_Click()
    End
End Sub

Private Sub Command2_Click()
    If List3.ListIndex < 0 Then
        If List3.ListCount > 0 Then
            List3.ListIndex = 0
        Else
            Exit Sub
        End If
    End If
    Dim i, c
    For i = 0 To List1.ListCount - 1
        PackageFiles(List3.ListIndex) = PackageFiles(List3.ListIndex) & List1.List(i) & ";"
        List2.AddItem List1.List(i)
    Next i
    List1.Clear
End Sub

Private Sub Command3_Click()
    If List3.ListIndex < 0 Then
        If List3.ListCount > 0 Then
            List3.ListIndex = 0
        Else
            Exit Sub
        End If
    End If
    Dim i
    For i = 0 To List2.ListCount - 1
        PackageFiles(List3.ListIndex) = Replace(PackageFiles(List3.ListIndex), List2.List(i) & ";", "")
        List1.AddItem List2.List(i)
    Next i
    List2.Clear
End Sub

Private Sub Command4_Click()
    If List2.ListIndex < 0 Then
        If List2.ListCount > 0 Then
            List2.ListIndex = 0
        Else
            Exit Sub
        End If
    End If
    If List3.ListIndex < 0 Then
        If List3.ListCount > 0 Then
            List3.ListIndex = 0
        Else
            Exit Sub
        End If
    End If
    Dim i
    For i = List2.ListCount - 1 To 0 Step -1
        If List2.Selected(i) Then
            PackageFiles(List3.ListIndex) = Replace(PackageFiles(List3.ListIndex), List2.List(i) & ";", "")
            List1.AddItem List2.List(i)
            List2.RemoveItem i
        End If
    Next i
End Sub

Private Sub Command5_Click()
    Dim s As String
    s = InputBox("Please enter the name of the new package:", "New Package", "Untitled")
    If s = "" Then Exit Sub
    Dim i
    For i = 0 To List3.ListCount - 1
        If Trim(UCase(List3.List(i))) = Trim(UCase(s)) Then
            MsgBox "A package with the same name already exists. Please select a different name!", vbInformation, "Error"
            Exit Sub
        End If
    Next i
    List3.AddItem s
    ReDim Preserve PackageFiles(Packages)
    Packages = Packages + 1
End Sub

Private Sub Command6_Click()
    If List3.ListIndex < 0 Then Exit Sub
    Dim Temp() As String, f() As String, q
    Dim i, j
    j = 0
    If List3.ListCount >= 2 Then
        ReDim Temp(List3.ListCount - 2)
        For i = 0 To List3.ListCount - 1
            If i <> List3.ListIndex Then
                Temp(j) = PackageFiles(i)
                j = j + 1
            Else
                f = Split(PackageFiles(i), ";")
                For Each q In f
                    If q <> "" Then List1.AddItem q
                Next q
            End If
        Next i
    Else
        f = Split(PackageFiles(0), ";")
        For Each q In f
            If q <> "" Then List1.AddItem q
        Next q
    End If
    List2.Clear
    List3.RemoveItem List3.ListIndex
    If List3.ListCount >= 1 Then
        ReDim PackageFiles(List3.ListCount - 1)
        For i = 0 To List3.ListCount - 1
            PackageFiles(i) = Temp(i)
        Next i
    End If
    If List3.ListCount > 0 Then List3.ListIndex = 0
End Sub

Private Sub Command7_Click()
    Packages = List1.ListCount
    ReDim PackageFiles(List1.ListCount - 1)
    Dim i
    For i = 0 To List1.ListCount - 1
        List3.AddItem List1.List(i)
        PackageFiles(i) = List1.List(i) & ";"
    Next i
    List1.Clear
    List3.ListIndex = 0
End Sub

Private Sub Command9_Click()
    Packages = 0
    LoadCategories
    List2.Clear
    List3.Clear
    ReDim PackageFiles(0)
End Sub

Private Sub Form_Load()
    LoadCategories
End Sub

Private Sub LoadCategories()
    Dim s As String
    s = Dir("*-0-0.gif")
    List1.Clear
    Do While s <> ""
        List1.AddItem Left(s, Len(s) - 8)
        s = Dir
    Loop
    
    Dim i
    ReDim RealNames(List1.ListCount - 1)
    For i = 0 To List1.ListCount - 1
        RealNames(i) = List1.List(i)
    Next i
End Sub

Private Sub List1_DblClick()
    Dim s As String
    s = InputBox("Enter the new name you want to assign to this tileset:", "Tileset rename", RealNames(List1.ListIndex))
    If s = "" Then Exit Sub
    List1.List(List1.ListIndex) = s
End Sub

Private Sub List3_Click()
    Dim f() As String, q
    f = Split(PackageFiles(List3.ListIndex), ";")
    List2.Clear
    For Each q In f
        If q <> "" Then List2.AddItem q
    Next q
End Sub

Private Sub List3_DblClick()
    Dim s As String
    s = InputBox("Enter the new name you want to assign to this package:", "Package rename", List3.List(List3.ListIndex))
    If s = "" Then Exit Sub
    List3.List(List3.ListIndex) = s
End Sub

