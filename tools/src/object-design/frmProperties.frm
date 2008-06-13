VERSION 5.00
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form frmProperties 
   BorderStyle     =   3  'Fixed Dialog
   Caption         =   "Object Information"
   ClientHeight    =   3360
   ClientLeft      =   45
   ClientTop       =   330
   ClientWidth     =   8445
   BeginProperty Font 
      Name            =   "Tahoma"
      Size            =   8.25
      Charset         =   161
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   3360
   ScaleWidth      =   8445
   ShowInTaskbar   =   0   'False
   StartUpPosition =   2  'CenterScreen
   Begin VB.CommandButton Command1 
      Cancel          =   -1  'True
      Caption         =   "Close"
      Default         =   -1  'True
      Height          =   615
      Left            =   7080
      TabIndex        =   20
      Top             =   2640
      Width           =   1215
   End
   Begin VB.Frame Frame3 
      Caption         =   "Gameplay information"
      Height          =   2055
      Left            =   4920
      TabIndex        =   14
      Top             =   480
      Width           =   3375
      Begin VB.TextBox Text3 
         Enabled         =   0   'False
         Height          =   285
         Left            =   2160
         TabIndex        =   19
         Text            =   "0"
         Top             =   1200
         Width           =   735
      End
      Begin VB.CheckBox Check4 
         Caption         =   "Overall attennuation   :                    %"
         Enabled         =   0   'False
         Height          =   255
         Left            =   120
         TabIndex        =   18
         Top             =   1200
         Width           =   3135
      End
      Begin VB.TextBox Text2 
         Enabled         =   0   'False
         Height          =   285
         Left            =   2160
         TabIndex        =   17
         Text            =   "12"
         Top             =   720
         Width           =   735
      End
      Begin VB.CheckBox Check3 
         Caption         =   "Object base elevation :                    m"
         Enabled         =   0   'False
         Height          =   255
         Left            =   120
         TabIndex        =   16
         Top             =   720
         Width           =   3135
      End
      Begin VB.CheckBox Check2 
         Caption         =   "Flatten object elevations to zero"
         Enabled         =   0   'False
         Height          =   255
         Left            =   120
         TabIndex        =   15
         Top             =   240
         Width           =   3135
      End
   End
   Begin VB.Frame Frame2 
      Caption         =   "Extra features"
      Height          =   615
      Left            =   120
      TabIndex        =   12
      Top             =   2640
      Width           =   4695
      Begin VB.CheckBox Check1 
         Caption         =   "Tile randomization (Usefull for terrains)"
         Enabled         =   0   'False
         Height          =   255
         Left            =   120
         TabIndex        =   13
         Top             =   240
         Width           =   4455
      End
   End
   Begin MSComctlLib.ImageList ImageList1 
      Left            =   4080
      Top             =   1320
      _ExtentX        =   1005
      _ExtentY        =   1005
      BackColor       =   -2147483643
      ImageWidth      =   33
      ImageHeight     =   33
      MaskColor       =   12632256
      _Version        =   393216
      BeginProperty Images {2C247F25-8591-11D1-B16A-00C0F0283628} 
         NumListImages   =   6
         BeginProperty ListImage1 {2C247F27-8591-11D1-B16A-00C0F0283628} 
            Picture         =   "frmProperties.frx":0000
            Key             =   ""
         EndProperty
         BeginProperty ListImage2 {2C247F27-8591-11D1-B16A-00C0F0283628} 
            Picture         =   "frmProperties.frx":0D36
            Key             =   ""
         EndProperty
         BeginProperty ListImage3 {2C247F27-8591-11D1-B16A-00C0F0283628} 
            Picture         =   "frmProperties.frx":1A6C
            Key             =   ""
         EndProperty
         BeginProperty ListImage4 {2C247F27-8591-11D1-B16A-00C0F0283628} 
            Picture         =   "frmProperties.frx":27A2
            Key             =   ""
         EndProperty
         BeginProperty ListImage5 {2C247F27-8591-11D1-B16A-00C0F0283628} 
            Picture         =   "frmProperties.frx":34D8
            Key             =   ""
         EndProperty
         BeginProperty ListImage6 {2C247F27-8591-11D1-B16A-00C0F0283628} 
            Picture         =   "frmProperties.frx":420E
            Key             =   ""
         EndProperty
      EndProperty
   End
   Begin VB.Frame Frame1 
      Caption         =   "Resize Model"
      Height          =   2055
      Left            =   120
      TabIndex        =   2
      Top             =   480
      Width           =   4695
      Begin VB.PictureBox Picture2 
         AutoSize        =   -1  'True
         Height          =   555
         Left            =   3960
         Picture         =   "frmProperties.frx":4F44
         ScaleHeight     =   495
         ScaleWidth      =   495
         TabIndex        =   11
         Top             =   240
         Width           =   555
      End
      Begin VB.PictureBox Picture1 
         BorderStyle     =   0  'None
         Height          =   975
         Left            =   120
         ScaleHeight     =   975
         ScaleWidth      =   3855
         TabIndex        =   6
         Top             =   720
         Width           =   3855
         Begin VB.OptionButton optChunkModel 
            Caption         =   "Preserve only the center size"
            Enabled         =   0   'False
            Height          =   255
            Index           =   3
            Left            =   240
            TabIndex        =   10
            Top             =   720
            Width           =   3495
         End
         Begin VB.OptionButton optChunkModel 
            Caption         =   "Resize vertical sides"
            Enabled         =   0   'False
            Height          =   255
            Index           =   2
            Left            =   240
            TabIndex        =   9
            Top             =   480
            Width           =   3495
         End
         Begin VB.OptionButton optChunkModel 
            Caption         =   "Resize horizontal sides"
            Enabled         =   0   'False
            Height          =   255
            Index           =   1
            Left            =   240
            TabIndex        =   8
            Top             =   240
            Width           =   3495
         End
         Begin VB.OptionButton optChunkModel 
            Caption         =   "Resize only the center"
            Height          =   255
            Index           =   0
            Left            =   240
            TabIndex        =   7
            Top             =   0
            Value           =   -1  'True
            Width           =   3495
         End
      End
      Begin VB.OptionButton optResizeModel 
         Caption         =   "Separated chunk model"
         Height          =   255
         Index           =   1
         Left            =   120
         TabIndex        =   5
         Top             =   480
         Value           =   -1  'True
         Width           =   3735
      End
      Begin VB.OptionButton optResizeModel 
         Caption         =   "Balanced resizing"
         Enabled         =   0   'False
         Height          =   255
         Index           =   2
         Left            =   120
         TabIndex        =   4
         Top             =   1680
         Width           =   3735
      End
      Begin VB.OptionButton optResizeModel 
         Caption         =   "No resizing"
         Height          =   255
         Index           =   0
         Left            =   120
         TabIndex        =   3
         Top             =   240
         Width           =   3735
      End
   End
   Begin VB.TextBox Text1 
      Enabled         =   0   'False
      Height          =   285
      Left            =   1440
      TabIndex        =   1
      Text            =   "Untitled"
      Top             =   70
      Width           =   6975
   End
   Begin VB.Label Label1 
      Caption         =   "Reference name:"
      Height          =   255
      Left            =   120
      TabIndex        =   0
      Top             =   120
      Width           =   1455
   End
End
Attribute VB_Name = "frmProperties"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private Sub Command1_Click()
    If optResizeModel(0).Value Then
        frmMain.ResizeModel = szFixedSize
    ElseIf optResizeModel(2).Value Then
        frmMain.ResizeModel = szNormalStretch
    ElseIf optResizeModel(1).Value Then
        frmMain.ResizeModel = szBoxStretch
        If optChunkModel(0).Value Then frmMain.ResizeModelParm = szCenterStretch
        If optChunkModel(1).Value Then frmMain.ResizeModelParm = szHSideStretch
        If optChunkModel(2).Value Then frmMain.ResizeModelParm = szVSideStretch
        If optChunkModel(3).Value Then frmMain.ResizeModelParm = szBothSideStretch
    End If
    
    Unload Me
End Sub

Private Sub Form_Load()
    If frmMain.ResizeModel = szFixedSize Then optResizeModel(0).Value = True
    If frmMain.ResizeModel = szNormalStretch Then optResizeModel(2).Value = True
    If frmMain.ResizeModel = szBoxStretch Then
        optResizeModel(1).Value = True
        If frmMain.ResizeModelParm = szCenterStretch Then optChunkModel(0).Value = True
        If frmMain.ResizeModelParm = szHSideStretch Then optChunkModel(1).Value = True
        If frmMain.ResizeModelParm = szVSideStretch Then optChunkModel(2).Value = True
        If frmMain.ResizeModelParm = szBothSideStretch Then optChunkModel(3).Value = True
    End If
    UpdatePreview
End Sub

Private Sub optChunkModel_Click(Index As Integer)
    UpdatePreview
End Sub

Private Sub optResizeModel_Click(Index As Integer)
    UpdatePreview
End Sub

Private Sub UpdatePreview()
    Dim ID, i
    
    If optResizeModel(0).Value Then
        ID = 1
    ElseIf optResizeModel(2).Value Then
        ID = 2
    ElseIf optResizeModel(1).Value Then
        If optChunkModel(0).Value Then ID = 3
        If optChunkModel(1).Value Then ID = 4
        If optChunkModel(2).Value Then ID = 5
        If optChunkModel(3).Value Then ID = 6
    End If
    
    Set Picture2.Picture = ImageList1.ListImages(ID).Picture
End Sub
