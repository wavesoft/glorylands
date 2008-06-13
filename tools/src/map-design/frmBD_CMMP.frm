VERSION 5.00
Begin VB.Form frmBD_CMMP 
   BorderStyle     =   3  'Fixed Dialog
   Caption         =   "Build Details - Serialized Chunk-Model Array"
   ClientHeight    =   1950
   ClientLeft      =   45
   ClientTop       =   330
   ClientWidth     =   4080
   ControlBox      =   0   'False
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
   ScaleHeight     =   1950
   ScaleWidth      =   4080
   ShowInTaskbar   =   0   'False
   StartUpPosition =   2  'CenterScreen
   Begin VB.CommandButton Command1 
      Caption         =   "Continue >>"
      Default         =   -1  'True
      Height          =   375
      Left            =   2400
      TabIndex        =   5
      Top             =   1440
      Width           =   1575
   End
   Begin VB.Frame Frame1 
      Caption         =   "Chunk dimensions"
      Height          =   1215
      Left            =   120
      TabIndex        =   0
      Top             =   120
      Width           =   3855
      Begin VB.TextBox Text2 
         Height          =   285
         Left            =   1200
         TabIndex        =   4
         Text            =   "8"
         Top             =   720
         Width           =   615
      End
      Begin VB.TextBox Text1 
         Height          =   285
         Left            =   1200
         TabIndex        =   2
         Text            =   "8"
         Top             =   320
         Width           =   615
      End
      Begin VB.Label Label2 
         Caption         =   "Chunk height :                tiles"
         Height          =   255
         Left            =   120
         TabIndex        =   3
         Top             =   765
         Width           =   2175
      End
      Begin VB.Label Label1 
         Caption         =   "Chunk width :                 tiles"
         Height          =   255
         Left            =   120
         TabIndex        =   1
         Top             =   360
         Width           =   2175
      End
   End
End
Attribute VB_Name = "frmBD_CMMP"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private Sub Command1_Click()
    SaveSetting App.Title, "compier", "CMMPWidth", Text1.Text
    SaveSetting App.Title, "compier", "CMMPHeight", Text2.Text
    Me.Hide
End Sub

Private Sub Form_Load()
    Text1.Text = GetSetting(App.Title, "compier", "CMMPWidth", "8")
    Text2.Text = GetSetting(App.Title, "compier", "CMMPHeight", "8")
End Sub

Private Sub Text1_GotFocus()
    Text1.SelStart = 0
    Text1.SelLength = Len(Text1.Text) + 1
End Sub

Private Sub Text2_GotFocus()
    Text2.SelStart = 0
    Text2.SelLength = Len(Text2.Text) + 1
End Sub
