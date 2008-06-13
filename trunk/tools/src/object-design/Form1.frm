VERSION 5.00
Object = "{F9043C88-F6F2-101A-A3C9-08002B2F49FB}#1.2#0"; "comdlg32.ocx"
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form frmMain 
   BorderStyle     =   1  'Fixed Single
   Caption         =   "SlimObject Designer - Untitled"
   ClientHeight    =   7425
   ClientLeft      =   45
   ClientTop       =   330
   ClientWidth     =   13050
   BeginProperty Font 
      Name            =   "Tahoma"
      Size            =   8.25
      Charset         =   161
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   Icon            =   "Form1.frx":0000
   KeyPreview      =   -1  'True
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   7425
   ScaleWidth      =   13050
   StartUpPosition =   2  'CenterScreen
   Begin VB.PictureBox Picture6 
      BackColor       =   &H00000000&
      BorderStyle     =   0  'None
      Height          =   1215
      Left            =   8760
      ScaleHeight     =   1215
      ScaleWidth      =   4215
      TabIndex        =   37
      Top             =   5880
      Width           =   4215
      Begin Project1.ctlFuzzySlider ctlFuzzySlider1 
         Height          =   615
         Left            =   120
         TabIndex        =   39
         Top             =   360
         Width           =   3975
         _ExtentX        =   6800
         _ExtentY        =   1085
         Caption         =   "Attennuation:"
      End
      Begin VB.Label Label2 
         Alignment       =   2  'Center
         BackColor       =   &H00404040&
         Caption         =   "Special Info"
         ForeColor       =   &H00FFFFFF&
         Height          =   255
         Left            =   0
         TabIndex        =   38
         Top             =   0
         Width           =   1455
      End
   End
   Begin MSComctlLib.StatusBar StatusBar1 
      Align           =   2  'Align Bottom
      Height          =   255
      Left            =   0
      TabIndex        =   36
      Top             =   7170
      Width           =   13050
      _ExtentX        =   23019
      _ExtentY        =   450
      _Version        =   393216
      BeginProperty Panels {8E3867A5-8586-11D1-B16A-00C0F0283628} 
         NumPanels       =   3
         BeginProperty Panel1 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            AutoSize        =   1
            Object.Width           =   20532
         EndProperty
         BeginProperty Panel2 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Bevel           =   2
            Object.Width           =   1058
            MinWidth        =   1058
            Text            =   "ZPoint"
            TextSave        =   "ZPoint"
         EndProperty
         BeginProperty Panel3 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            Object.Width           =   1324
            MinWidth        =   1324
            Text            =   "Modify Z"
            TextSave        =   "Modify Z"
         EndProperty
      EndProperty
   End
   Begin Project1.ctlBrowseTiles ctlBrowseTiles1 
      Height          =   5655
      Left            =   8760
      TabIndex        =   35
      Top             =   120
      Width           =   3630
      _ExtentX        =   6403
      _ExtentY        =   12303
   End
   Begin VB.PictureBox Picture4 
      BackColor       =   &H8000000C&
      BorderStyle     =   0  'None
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   3975
      Left            =   840
      ScaleHeight     =   3975
      ScaleWidth      =   3975
      TabIndex        =   28
      Top             =   0
      Visible         =   0   'False
      Width           =   3975
      Begin VB.CommandButton Command11 
         Caption         =   "Browse ..."
         Height          =   375
         Left            =   2160
         Style           =   1  'Graphical
         TabIndex        =   34
         Top             =   3480
         Width           =   1695
      End
      Begin VB.CommandButton Command10 
         Caption         =   "&Cancel"
         Height          =   615
         Left            =   2160
         Picture         =   "Form1.frx":B84A
         Style           =   1  'Graphical
         TabIndex        =   33
         Top             =   2640
         Width           =   1695
      End
      Begin VB.CommandButton Command9 
         Caption         =   "&OK"
         Height          =   615
         Left            =   2160
         Picture         =   "Form1.frx":BDD4
         Style           =   1  'Graphical
         TabIndex        =   32
         Top             =   1920
         Width           =   1695
      End
      Begin VB.PictureBox Picture5 
         BackColor       =   &H00FFFFFF&
         Height          =   1935
         Left            =   120
         ScaleHeight     =   1875
         ScaleWidth      =   1875
         TabIndex        =   31
         Top             =   1920
         Width           =   1935
         Begin VB.Image Image1 
            Height          =   1095
            Left            =   480
            Top             =   360
            Width           =   855
         End
      End
      Begin VB.ListBox List1 
         Appearance      =   0  'Flat
         BackColor       =   &H80000015&
         ForeColor       =   &H8000000E&
         Height          =   1395
         Left            =   120
         TabIndex        =   30
         Top             =   360
         Width           =   3735
      End
      Begin VB.Label Label1 
         BackStyle       =   0  'Transparent
         Caption         =   "Saved projects :"
         ForeColor       =   &H8000000E&
         Height          =   255
         Left            =   120
         TabIndex        =   29
         Top             =   120
         Width           =   1815
      End
   End
   Begin MSComDlg.CommonDialog CommonDialog1 
      Left            =   6240
      Top             =   3360
      _ExtentX        =   847
      _ExtentY        =   847
      _Version        =   393216
      CancelError     =   -1  'True
   End
   Begin VB.CommandButton Command6 
      Caption         =   "Preview"
      Height          =   615
      Left            =   7560
      TabIndex        =   21
      Top             =   6480
      Width           =   1095
   End
   Begin VB.PictureBox Picture2 
      Align           =   3  'Align Left
      BackColor       =   &H8000000C&
      BorderStyle     =   0  'None
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   7170
      Left            =   0
      ScaleHeight     =   7170
      ScaleWidth      =   855
      TabIndex        =   16
      Top             =   0
      Width           =   855
      Begin VB.CommandButton Command8 
         Caption         =   "Compile"
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   6.75
            Charset         =   161
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   615
         Left            =   120
         Picture         =   "Form1.frx":C35E
         Style           =   1  'Graphical
         TabIndex        =   27
         TabStop         =   0   'False
         Top             =   3000
         Width           =   615
      End
      Begin VB.CommandButton Command7 
         Caption         =   "Save"
         Height          =   615
         Left            =   120
         Picture         =   "Form1.frx":C8E8
         Style           =   1  'Graphical
         TabIndex        =   22
         TabStop         =   0   'False
         Top             =   2280
         Width           =   615
      End
      Begin VB.CommandButton Command4 
         Caption         =   "Quit"
         Height          =   615
         Left            =   120
         Picture         =   "Form1.frx":CE72
         Style           =   1  'Graphical
         TabIndex        =   20
         TabStop         =   0   'False
         Top             =   6480
         Width           =   615
      End
      Begin VB.CommandButton Command3 
         Caption         =   "Save.."
         Height          =   615
         Left            =   120
         Picture         =   "Form1.frx":D3FC
         Style           =   1  'Graphical
         TabIndex        =   19
         TabStop         =   0   'False
         Top             =   1560
         Width           =   615
      End
      Begin VB.CommandButton Command2 
         Caption         =   "Open"
         Height          =   615
         Left            =   120
         Picture         =   "Form1.frx":D986
         Style           =   1  'Graphical
         TabIndex        =   18
         TabStop         =   0   'False
         Top             =   840
         Width           =   615
      End
      Begin VB.CommandButton Command1 
         Caption         =   "New"
         Height          =   615
         Left            =   120
         Picture         =   "Form1.frx":DF10
         Style           =   1  'Graphical
         TabIndex        =   17
         TabStop         =   0   'False
         Top             =   120
         Width           =   615
      End
      Begin VB.PictureBox Picture3 
         BorderStyle     =   0  'None
         BeginProperty Font 
            Name            =   "MS Sans Serif"
            Size            =   8.25
            Charset         =   161
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   2415
         Left            =   0
         ScaleHeight     =   2415
         ScaleWidth      =   855
         TabIndex        =   23
         Top             =   3960
         Width           =   855
         Begin VB.CommandButton Command5 
            Caption         =   "Object Info"
            Height          =   855
            Left            =   120
            Picture         =   "Form1.frx":E49A
            Style           =   1  'Graphical
            TabIndex        =   26
            TabStop         =   0   'False
            Top             =   1440
            Width           =   615
         End
         Begin VB.OptionButton Option2 
            Caption         =   "Place"
            Height          =   615
            Left            =   120
            Picture         =   "Form1.frx":EA24
            Style           =   1  'Graphical
            TabIndex        =   25
            Top             =   120
            Value           =   -1  'True
            Width           =   615
         End
         Begin VB.OptionButton Option1 
            Caption         =   "Erase"
            Height          =   615
            Left            =   120
            Picture         =   "Form1.frx":EFAE
            Style           =   1  'Graphical
            TabIndex        =   24
            Top             =   720
            Width           =   615
         End
      End
   End
   Begin VB.PictureBox Picture1 
      BorderStyle     =   0  'None
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   375
      Left            =   960
      ScaleHeight     =   375
      ScaleWidth      =   7695
      TabIndex        =   10
      Top             =   120
      Width           =   7695
      Begin VB.CheckBox Check1 
         Caption         =   "Show only current layer"
         Height          =   375
         Left            =   5520
         Style           =   1  'Graphical
         TabIndex        =   15
         Top             =   0
         Width           =   2175
      End
      Begin VB.OptionButton optLayer 
         Caption         =   "Layer 4"
         Height          =   375
         Index           =   3
         Left            =   3600
         Style           =   1  'Graphical
         TabIndex        =   14
         Top             =   0
         Width           =   1215
      End
      Begin VB.OptionButton optLayer 
         Caption         =   "Layer 3"
         Height          =   375
         Index           =   2
         Left            =   2400
         Style           =   1  'Graphical
         TabIndex        =   13
         Top             =   0
         Width           =   1215
      End
      Begin VB.OptionButton optLayer 
         Caption         =   "Layer 2"
         Height          =   375
         Index           =   1
         Left            =   1200
         Style           =   1  'Graphical
         TabIndex        =   12
         Top             =   0
         Width           =   1215
      End
      Begin VB.OptionButton optLayer 
         Caption         =   "Layer 1"
         Height          =   375
         Index           =   0
         Left            =   0
         Style           =   1  'Graphical
         TabIndex        =   11
         Top             =   0
         Value           =   -1  'True
         Width           =   1215
      End
   End
   Begin VB.OptionButton picTab 
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   615
      Index           =   8
      Left            =   6720
      Picture         =   "Form1.frx":F538
      Style           =   1  'Graphical
      TabIndex        =   1
      Top             =   6480
      Width           =   735
   End
   Begin VB.OptionButton picTab 
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   615
      Index           =   7
      Left            =   6000
      Picture         =   "Form1.frx":F5EE
      Style           =   1  'Graphical
      TabIndex        =   2
      Top             =   6480
      Width           =   735
   End
   Begin VB.OptionButton picTab 
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   615
      Index           =   6
      Left            =   5280
      Picture         =   "Form1.frx":F6A4
      Style           =   1  'Graphical
      TabIndex        =   3
      Top             =   6480
      Width           =   735
   End
   Begin VB.OptionButton picTab 
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   615
      Index           =   5
      Left            =   4560
      Picture         =   "Form1.frx":F75A
      Style           =   1  'Graphical
      TabIndex        =   4
      Top             =   6480
      Width           =   735
   End
   Begin VB.OptionButton picTab 
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   615
      Index           =   4
      Left            =   3840
      Picture         =   "Form1.frx":F813
      Style           =   1  'Graphical
      TabIndex        =   5
      Top             =   6480
      Width           =   735
   End
   Begin VB.OptionButton picTab 
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   615
      Index           =   3
      Left            =   3120
      Picture         =   "Form1.frx":F8CC
      Style           =   1  'Graphical
      TabIndex        =   6
      Top             =   6480
      Width           =   735
   End
   Begin VB.OptionButton picTab 
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   615
      Index           =   2
      Left            =   2400
      Picture         =   "Form1.frx":F984
      Style           =   1  'Graphical
      TabIndex        =   7
      Top             =   6480
      Width           =   735
   End
   Begin VB.OptionButton picTab 
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   615
      Index           =   1
      Left            =   1680
      Picture         =   "Form1.frx":FA33
      Style           =   1  'Graphical
      TabIndex        =   8
      Top             =   6480
      Width           =   735
   End
   Begin VB.OptionButton picTab 
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   615
      Index           =   0
      Left            =   960
      Picture         =   "Form1.frx":FAE9
      Style           =   1  'Graphical
      TabIndex        =   9
      Top             =   6480
      Value           =   -1  'True
      Width           =   735
   End
   Begin VB.PictureBox picDesigner 
      Appearance      =   0  'Flat
      AutoRedraw      =   -1  'True
      BackColor       =   &H80000005&
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H80000008&
      Height          =   5775
      Left            =   960
      ScaleHeight     =   5745
      ScaleWidth      =   7665
      TabIndex        =   0
      Top             =   600
      Width           =   7695
      Begin VB.Line xlHov1 
         BorderColor     =   &H000000FF&
         BorderWidth     =   2
         Visible         =   0   'False
         X1              =   4560
         X2              =   5040
         Y1              =   1800
         Y2              =   2040
      End
      Begin VB.Line xlHov2 
         BorderColor     =   &H000000FF&
         BorderWidth     =   2
         Visible         =   0   'False
         X1              =   4560
         X2              =   5040
         Y1              =   2040
         Y2              =   1800
      End
      Begin VB.Line xl2 
         BorderWidth     =   2
         DrawMode        =   6  'Mask Pen Not
         X1              =   4920
         X2              =   5400
         Y1              =   1440
         Y2              =   1200
      End
      Begin VB.Line xl1 
         BorderWidth     =   2
         DrawMode        =   6  'Mask Pen Not
         X1              =   4920
         X2              =   5400
         Y1              =   1200
         Y2              =   1440
      End
      Begin VB.Shape Shape2 
         BorderColor     =   &H00800000&
         BorderWidth     =   2
         Height          =   15
         Left            =   0
         Top             =   0
         Width           =   15
      End
      Begin VB.Image img 
         Height          =   495
         Index           =   0
         Left            =   6360
         Top             =   2160
         Width           =   375
      End
      Begin VB.Shape Shape1 
         BorderColor     =   &H00000000&
         DrawMode        =   6  'Mask Pen Not
         FillStyle       =   0  'Solid
         Height          =   1935
         Left            =   600
         Top             =   720
         Width           =   2895
      End
   End
End
Attribute VB_Name = "frmMain"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
Dim MouseX As Integer, MouseY As Integer
Dim ZX As Integer, ZY As Integer, ZDiff As Integer

Dim iSelX As Integer, iSelY As Integer, iSelW As Integer, iSelH As Integer, iBmp() As StdPicture, iNames() As String

Dim WithEvents ImageBlit As clsImgGridObj
Attribute ImageBlit.VB_VarHelpID = -1

Public ResizeModel As enmResizeModels
Public ResizeModelParm As enmResizeModelParm

Dim CurGrid As Integer, CurLayer As Byte
Dim FileHead As ObjectHead

Dim IsChanged As Boolean
Dim LastFile As String
Dim CtrlDown As Boolean
Dim LastObjFile As String

Dim ShowZPoint As Boolean
Dim ChangeZ As Boolean

Private Sub Check1_Click()
    BlitGrid CurGrid
End Sub

Private Sub Command1_Click()
    If MsgBox("Do you really want to erase the whole project?", vbQuestion Or vbYesNo Or vbDefaultButton1, "New Project") = vbYes Then
        Dim X, Y, l, i
        For i = 0 To 8
            For l = 0 To 3
                For X = 0 To 15
                    For Y = 0 To 11
                        Grids(i).ActualH = 0
                        Grids(i).ActualW = 0
                        Grids(i).ActualL = 0
                        Grids(i).Grid(l, X, Y) = ""
                        Set Grids(i).RAMGrid(l, X, Y) = Nothing
                    Next Y
                Next X
            Next l
        Next i
        picTab(0).Value = True
        Check1.Value = 0
        optLayer(0).Value = True
        Option2.Value = True
        picTab_Click 0
        IsChanged = False
        LastFile = ""
        LastObjFile = ""
        Me.Caption = "SlimObject Designer - Untitled"
    End If
End Sub

Private Sub Command2_Click()
    On Error GoTo er
    If LastFile <> "" Then
        CommonDialog1.Filename = LastFile
    Else
        If Dir(FixPath(App.Path) & "objects", vbDirectory) <> "" Then
            CommonDialog1.Filename = FixPath(App.Path) & "objects\*.ob"
        End If
    End If
    CommonDialog1.DialogTitle = "Open object file"
    CommonDialog1.Filter = "Map object file (*.ob) | *.ob"
    CommonDialog1.flags = cdlOFNExplorer Or cdlOFNFileMustExist Or cdlOFNPathMustExist
    CommonDialog1.ShowOpen
    LoadFrom CommonDialog1.Filename
    LastFile = CommonDialog1.Filename
    Me.Caption = "SlimObject Designer - " & GetFileName(LastFile)
    IsChanged = False
    picTab(0).Value = True
    picTab_Click 0
er:
End Sub

Private Sub Command3_Click()
    On Error GoTo er
    If LastFile <> "" Then
        CommonDialog1.Filename = LastFile
    Else
        If Dir(FixPath(App.Path) & "objects", vbDirectory) <> "" Then
            CommonDialog1.Filename = FixPath(App.Path) & "objects\untitled.ob"
        End If
    End If
    CommonDialog1.DialogTitle = "Save object to file"
    CommonDialog1.Filter = "Map object file (*.ob) | *.ob"
    CommonDialog1.flags = cdlOFNExplorer Or cdlOFNOverwritePrompt Or cdlOFNPathMustExist Or cdlOFNExtensionDifferent
    CommonDialog1.ShowSave
    SaveTo CommonDialog1.Filename
    LastFile = CommonDialog1.Filename
    Me.Caption = "SlimObject Designer - " & GetFileName(LastFile)
    IsChanged = False
er:
End Sub

Private Sub Command4_Click()
    If IsChanged Then
        Dim Ans As VbMsgBoxResult
        Ans = MsgBox("Do you want to save the changes before quitting?", vbYesNoCancel Or vbQuestion Or vbDefaultButton2, "Quit")
        If Ans = vbYes Then
            Command7_Click
        ElseIf Ans = vbCancel Then
            Exit Sub
        End If
    End If
    End
End Sub

Private Sub Command5_Click()
    frmProperties.Show vbModal
End Sub

Private Sub Command6_Click()
    Unload frmPreview
    FindAllExtents
    frmPreview.Show
End Sub

Private Sub Command7_Click()
    If LastFile = "" Then
        Command3_Click
    Else
        SaveTo LastFile
        IsChanged = False
        Me.Caption = "SlimObject Designer - " & GetFileName(LastFile)
    End If
End Sub

Private Sub Changed()
    If Not IsChanged Then
        IsChanged = True
        Me.Caption = Me.Caption & "*"
    End If
End Sub

Private Sub Command8_Click()
    Dim O As New clsObjIO
    On Error GoTo er
    If LastObjFile <> "" Then
        CommonDialog1.Filename = LastObjFile
    ElseIf LastObjFile = "" And LastFile <> "" Then
        CommonDialog1.Filename = Left(LastFile, Len(LastFile) - 3) & ".cob"
    Else
        If Dir(FixPath(App.Path) & "objects", vbDirectory) <> "" Then
            CommonDialog1.Filename = FixPath(App.Path) & "objects\untitled.cob"
        End If
    End If
    CommonDialog1.DialogTitle = "Save compiled object"
    CommonDialog1.Filter = "Map object file (*.cob) | *.cob"
    CommonDialog1.flags = cdlOFNExplorer Or cdlOFNOverwritePrompt Or cdlOFNPathMustExist Or cdlOFNExtensionDifferent
    CommonDialog1.ShowSave
    frmProperties.Show 1
    LastObjFile = CommonDialog1.Filename
    Set O.Preview = frmPreview.GetPreview()
    O.ResizeModel = ResizeModel
    O.ResizeModelParm = ResizeModelParm
    O.SaveFile CommonDialog1.Filename
er:
End Sub

Private Sub ctlBrowseTiles1_SelectionChange(Category As String, SelX As Integer, SelY As Integer, SelW As Integer, SelH As Integer, TileBmp() As stdole.StdPicture, TileName() As String, BlockImg As stdole.StdPicture)
    iSelX = SelX
    iSelY = SelY
    iSelW = SelW
    iSelH = SelH
    iBmp = TileBmp
    iNames = TileName
    If iSelW > 0 And iSelH > 0 Then
        Shape1.Width = 32 * iSelW
        Shape1.Height = 32 * iSelH
    End If
    'optLayer(0).Value = True
    Option2.Value = True
End Sub

Private Sub Form_KeyDown(KeyCode As Integer, Shift As Integer)
    Dim i, c
    c = 0
    For i = 0 To picTab.Count - 1
        If picTab(i).Value Then c = i
    Next i
    
    ' [UP]
    If KeyCode = 38 Then
'        ShiftUp
        
    ' [DOWN]
    ElseIf KeyCode = 40 Then
'        ShiftDown
        
    ' [LEFT]
    ElseIf KeyCode = 37 Then
        If c = 0 Then
            c = picTab.UBound
        Else
            c = c - 1
        End If
        picTab(c).Value = True
        
    ' [RIGHT]
    ElseIf KeyCode = 39 Then
        If c = picTab.UBound Then
            c = 0
        Else
            c = c + 1
        End If
        picTab(c).Value = True
    
    End If
End Sub

Private Sub Form_KeyUp(KeyCode As Integer, Shift As Integer)
    StatusBar1.Panels(1).Text = ""
End Sub

Private Sub Form_Load()
    frmSplash.Show
    DoEvents
    Set ImageBlit = New clsImgGridObj
    ctlBrowseTiles1.Init
    ctlBrowseTiles1.Width = 10
    
    picDesigner.ScaleMode = vbPixels
    Shape1.Width = 33
    Shape1.Height = 33
    DrawGrid
    
    CurGrid = 0
    ChangeZ = True
    
    Me.Show
    DoEvents
    frmSplash.Show
End Sub

Private Function GetFileName(ByVal Path As String)
    Dim f() As String
    f = Split(Path, "\")
    If UBound(f) >= 0 Then
        GetFileName = f(UBound(f))
    Else
        GetFileName = Path
    End If
End Function

Private Sub DrawGrid()
    Dim X, Y
    For X = 32 To picDesigner.ScaleWidth Step 32
        picDesigner.Line (X, 0)-(X, picDesigner.ScaleHeight), &HC0C0C0
    Next X
    For Y = 32 To picDesigner.ScaleHeight Step 32
        picDesigner.Line (0, Y)-(picDesigner.ScaleWidth, Y), &HC0C0C0
    Next Y
    picDesigner.Line (0, 0)-(32, 32), &H8080FF, B
End Sub

Private Sub Form_QueryUnload(Cancel As Integer, UnloadMode As Integer)
    If UnloadMode = QueryUnloadConstants.vbFormControlMenu Then
        Cancel = 1
        Command4_Click
    End If
End Sub

Private Sub ImageBlit_AllocateImage(X As Integer, Y As Integer, Image As stdole.StdPicture, IndexID As Integer)
    Dim i
    i = img.UBound + 1
    Load img(i)
    img(i).Left = X
    img(i).Top = Y
    img(i).Visible = True
    Set img(i).Container = picDesigner
    Set img(i).Picture = Image
    IndexID = i
End Sub

Private Sub ImageBlit_AlterImage(Index As Integer, Image As stdole.StdPicture)
    Set img(Index) = Image
End Sub

Private Sub ImageBlit_DestroyImage(Index As Integer)
    On Error Resume Next
    img(Index).Visible = False
    Unload img(Index)
End Sub

Private Sub ImageBlit_ZOrderBack(Image As Integer)
    img(Image).ZOrder 1
End Sub

Private Sub img_MouseMove(Index As Integer, Button As Integer, Shift As Integer, X As Single, Y As Single)
    picDesigner_MouseMove Button, Shift, (X / Screen.TwipsPerPixelX) + img(Index).Left, (Y / Screen.TwipsPerPixelY) + img(Index).Top
End Sub

Private Sub img_MouseUp(Index As Integer, Button As Integer, Shift As Integer, X As Single, Y As Single)
    picDesigner_MouseUp Button, Shift, (X / Screen.TwipsPerPixelX) + img(Index).Left, (Y / Screen.TwipsPerPixelY) + img(Index).Top
End Sub

Private Sub optLayer_Click(Index As Integer)
    CurLayer = Index
    If Check1.Value Then BlitGrid CurGrid
End Sub

Private Sub picDesigner_MouseMove(Button As Integer, Shift As Integer, X As Single, Y As Single)
    MouseX = X \ 32
    MouseY = Y \ 32
    Shape1.Left = MouseX * 32
    Shape1.Top = MouseY * 32

    ' If nothing pressed, display current hover'ed item's info
    If Shift = 0 Then
        Dim cInf As COF_Map_Info
        If (MouseX < Grids(CurGrid).ActualW) And (MouseY < Grids(CurGrid).ActualH) Then
            cInf = Grids(CurGrid).MapInfo(MouseX, MouseY)
            StatusBar1.Panels(1).Text = "Tile [" & MouseX & "," & MouseY & "] has x=" & cInf.MapX & _
                    " y=" & cInf.MapY & _
                    " z=" & cInf.MapZ & _
                    " a=" & cInf.Attennuation
            If cInf.MapZ <> 0 And ShowZPoint Then
                XHVisible True
                XHMove cInf.MapX, cInf.MapY
            Else
                XHVisible False
            End If
        Else
            XHVisible False
            StatusBar1.Panels(1).Text = ""
        End If
    End If

    ' If CTRL is not pressed, move the Z Reference point
    ' among with current mouse position
    If (Shift And 2) <> 0 Then
        ZDiff = ZY - MouseY
        StatusBar1.Panels(1).Text = "Moving Z-Index to " & ZDiff
    Else
        ZX = MouseX
        ZY = MouseY + ZDiff
    End If
    XMove ZX, ZY
End Sub

Private Sub picDesigner_MouseUp(Button As Integer, Shift As Integer, X As Single, Y As Single)
    On Error Resume Next
    'Dim x, y
    PushTileOnGrid MouseX, MouseY, CurLayer, IIf(ChangeZ, ZDiff, 0), 0
    
    FindExtends CurGrid
    Shape2.Width = 32 * Grids(CurGrid).ActualW
    Shape2.Height = 32 * Grids(CurGrid).ActualH
    Changed
End Sub

Private Sub BlitGrid(ID As Integer)
    picDesigner.Cls
    ImageBlit.Truncate
    
    Dim X, Y, l
    For X = 0 To 15
        For Y = 0 To 11
            ' Only current layer?
            If Check1.Value = 1 Then
                If Not Grids(ID).RAMGrid(CurLayer, X, Y) Is Nothing Then
                    'picDesigner.PaintPicture Grids(ID).RAMGrid(CurLayer, X, Y), X * 32, Y * 32
                    ImageBlit.PenAction X, Y, CurLayer, Trim(Grids(ID).Grid(CurLayer, X, Y))
                End If
            
            ' Or the whole grid?
            Else
                For l = 0 To 3
                    If Not Grids(ID).RAMGrid(l, X, Y) Is Nothing Then
                        'picDesigner.PaintPicture Grids(ID).RAMGrid(l, X, Y), X * 32, Y * 32
                        ImageBlit.PenAction X, Y, l, Trim(Grids(ID).Grid(l, X, Y))
                    End If
                Next l
            End If
        Next Y
    Next X
    DrawGrid
End Sub

Private Sub SaveTo(Filename As String)
    Dim f As Long
    f = FreeFile
    FindAllExtents
    Open Filename For Output As #f
    Close #f
    Open Filename For Binary As #f
    Put #f, , FileHead
    Dim i
    For i = 0 To 8
        Put #f, , Grids(i).ActualH
        Put #f, , Grids(i).ActualW
        Put #f, , Grids(i).ActualL
        Put #f, , Grids(i).Grid
        Put #f, , Grids(i).MapInfo
    Next i
    Close #f
    
    IsChanged = False
    Me.Caption = "SlimObject Designer - " & GetFileName(LastFile)
End Sub

Private Sub LoadFrom(Filename As String)
    Dim f As Long
    f = FreeFile
    Open Filename For Binary As #f
    Get #f, , FileHead
    Dim i
    For i = 0 To 8
        Get #f, , Grids(i).ActualH
        Get #f, , Grids(i).ActualW
        Get #f, , Grids(i).ActualL
        Get #f, , Grids(i).Grid
        Get #f, , Grids(i).MapInfo
        RAMCacheGrid i
    Next i
    Close #f
    BlitGrid CurGrid
End Sub

Private Sub RAMCacheGrid(ByVal GridID As Integer)
    Dim X, Y, l
    frmLoading.Show
    frmLoading.Label2.Caption = "Loading RAM Cache for grid " & GridID
    DoEvents
    For X = 0 To 15
        For Y = 0 To 11
            For l = 0 To 3
                If Trim(Replace(Grids(GridID).Grid(l, X, Y), vbNullChar, "")) <> "" Then
                    Set Grids(GridID).RAMGrid(l, X, Y) = imgCache.Image(Trim(Grids(GridID).Grid(l, X, Y))) '                    LoadPicture (BasePath & Trim(Grids(GridID).Grid(l, x, y)))
                End If
            Next l
        Next Y
    Next X
    Unload frmLoading
End Sub

Private Sub PushTileOnGrid(ByVal PosX As Integer, ByVal PosY As Integer, ByVal Layer As Byte, ByVal ZIndex As Byte, ByVal Attennuation As Integer)
    Dim X, Y
    If iSelW = 0 And iSelH = 0 Then Exit Sub
    For X = PosX To PosX + iSelW - 1
        For Y = PosY To PosY + iSelH - 1
        
            If Option1.Value Then
                Grids(CurGrid).Grid(Layer, X, Y) = ""
                Set Grids(CurGrid).RAMGrid(Layer, X, Y) = Nothing
                ImageBlit.PenAction X, Y, CurLayer, "", , pmErase
            Else
        
                Grids(CurGrid).Grid(Layer, X, Y) = iNames(X - PosX, Y - PosY)
                Set Grids(CurGrid).RAMGrid(Layer, X, Y) = iBmp(X - PosX, Y - PosY)
                'DrawLayeredTile X, Y
                ImageBlit.PenAction X, Y, CurLayer, "", imgCache.Image(Trim(iNames(X - PosX, Y - PosY)))
            
                If X + 1 > Grids(CurGrid).ActualW Then Grids(CurGrid).ActualW = X + 1
                If Y + 1 > Grids(CurGrid).ActualH Then Grids(CurGrid).ActualH = Y + 1
                If Layer + 1 > Grids(CurGrid).ActualL Then Grids(CurGrid).ActualL = Layer + 1
            End If
            
            ' Store special map info
            Grids(CurGrid).MapInfo(X, Y).MapZ = IIf(ChangeZ, ZIndex - (Y - PosY), 0)
            Grids(CurGrid).MapInfo(X, Y).MapX = X
            Grids(CurGrid).MapInfo(X, Y).MapY = PosY + ZIndex
            Grids(CurGrid).MapInfo(X, Y).Attennuation = ctlFuzzySlider1.Value
            
        Next Y
    Next X
    ' Update actualXY coordinates
    ' Draw grid
    DrawGrid
End Sub

Private Sub FindAllExtents()
    Dim i
    For i = 0 To 8
        FindExtends i
    Next i
End Sub

Private Sub FindExtends(ByVal GridID As Integer)
    Dim X, Y, l
    Grids(GridID).ActualH = 0
    Grids(GridID).ActualW = 0
    Grids(GridID).ActualL = 0
    For l = 0 To 3
        For X = 0 To 15
            For Y = 0 To 11
                If Not Grids(GridID).RAMGrid(l, X, Y) Is Nothing Then
                    If X + 1 > Grids(GridID).ActualW Then Grids(GridID).ActualW = X + 1
                    If Y + 1 > Grids(GridID).ActualH Then Grids(GridID).ActualH = Y + 1
                    If l + 1 > Grids(GridID).ActualL Then Grids(GridID).ActualL = l + 1
                End If
            Next Y
        Next X
    Next l
End Sub

Private Sub DrawLayeredTile(ByVal PosX As Integer, ByVal PosY As Integer)
    Dim l As Integer, s As String
    
    ' Blit only current layer?
    If Check1.Value Then
        If Not Grids(CurGrid).RAMGrid(CurLayer, PosX, PosY) Is Nothing Then
            picDesigner.PaintPicture Grids(CurGrid).RAMGrid(CurLayer, PosX, PosY), PosX * 32, PosY * 32
        End If
    
    ' Or the whole grid?
    Else
        For l = 0 To 3
            If Not Grids(CurGrid).RAMGrid(l, PosX, PosY) Is Nothing Then
                picDesigner.PaintPicture Grids(CurGrid).RAMGrid(l, PosX, PosY), PosX * 32, PosY * 32
            End If
        Next l
    End If
End Sub

Private Sub picTab_Click(Index As Integer)
    CurGrid = Index
    BlitGrid Index
    Shape2.Width = 32 * Grids(CurGrid).ActualW
    Shape2.Height = 32 * Grids(CurGrid).ActualH
End Sub

Private Sub XVisible(ByVal Visible As Boolean)
    xl1.Visible = Visible
    xl2.Visible = Visible
End Sub

Private Sub XMove(ByVal X As Integer, ByVal Y As Integer)
    Dim rX, rY
    rX = X * 32 + 16
    rY = Y * 32 + 16
    
    xl1.X1 = rX - 8
    xl1.Y1 = rY - 8
    xl1.X2 = rX + 8
    xl1.Y2 = rY + 8
    
    xl2.X1 = rX + 8
    xl2.Y1 = rY - 8
    xl2.X2 = rX - 8
    xl2.Y2 = rY + 8
End Sub

Private Sub XHVisible(ByVal Visible As Boolean)
    xlHov1.Visible = Visible
    xlHov2.Visible = Visible
End Sub

Private Sub XHMove(ByVal X As Integer, ByVal Y As Integer)
    Dim rX, rY
    rX = X * 32 + 16
    rY = Y * 32 + 16
    
    xlHov1.X1 = rX - 8
    xlHov1.Y1 = rY - 8
    xlHov1.X2 = rX + 8
    xlHov1.Y2 = rY + 8
    
    xlHov2.X1 = rX + 8
    xlHov2.Y1 = rY - 8
    xlHov2.X2 = rX - 8
    xlHov2.Y2 = rY + 8
End Sub

Private Sub StatusBar1_PanelClick(ByVal Panel As MSComctlLib.Panel)
    If Panel.Index = 2 Then
        ShowZPoint = (Panel.Bevel = sbrInset)
        ShowZPoint = Not ShowZPoint
        Panel.Bevel = IIf(ShowZPoint, sbrInset, sbrRaised)
        If Not ShowZPoint Then XHVisible False
    ElseIf Panel.Index = 3 Then
        ChangeZ = (Panel.Bevel = sbrInset)
        ChangeZ = Not ChangeZ
        Panel.Bevel = IIf(ChangeZ, sbrInset, sbrRaised)
        XVisible ChangeZ
    End If
End Sub
