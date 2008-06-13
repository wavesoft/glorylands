VERSION 5.00
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.Form frmSplash 
   BorderStyle     =   3  'Fixed Dialog
   ClientHeight    =   750
   ClientLeft      =   45
   ClientTop       =   45
   ClientWidth     =   4515
   ControlBox      =   0   'False
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   750
   ScaleWidth      =   4515
   ShowInTaskbar   =   0   'False
   StartUpPosition =   2  'CenterScreen
   Begin MSComctlLib.ProgressBar ProgressBar1 
      Height          =   135
      Left            =   0
      TabIndex        =   1
      Top             =   600
      Width           =   4575
      _ExtentX        =   8070
      _ExtentY        =   238
      _Version        =   393216
      Appearance      =   0
   End
   Begin VB.Label Label1 
      BackStyle       =   0  'Transparent
      Caption         =   "Map build in progress. Please wait..."
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   255
      Left            =   720
      TabIndex        =   0
      Top             =   280
      Width           =   3615
   End
   Begin VB.Image Image1 
      Height          =   480
      Left            =   120
      Picture         =   "frmSplash.frx":0000
      Top             =   120
      Width           =   480
   End
End
Attribute VB_Name = "frmSplash"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private Sub Form_Load()
    ProgressBar1.Value = 0
End Sub
