VERSION 5.00
Begin VB.Form frmBD_CMP 
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Build Details - Serialized Array"
   ClientHeight    =   1995
   ClientLeft      =   45
   ClientTop       =   330
   ClientWidth     =   3915
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
   ScaleHeight     =   1995
   ScaleWidth      =   3915
   StartUpPosition =   2  'CenterScreen
   Begin VB.CommandButton Command1 
      Caption         =   "Continue >>"
      Default         =   -1  'True
      Height          =   375
      Left            =   2280
      TabIndex        =   7
      Top             =   1560
      Width           =   1575
   End
   Begin VB.Frame Frame1 
      Caption         =   "Compilation Process Details"
      Height          =   1335
      Left            =   120
      TabIndex        =   0
      Top             =   120
      Width           =   3735
      Begin VB.OptionButton Option3 
         Caption         =   "Level 3"
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   8.25
            Charset         =   161
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   120
         TabIndex        =   5
         Top             =   960
         Width           =   975
      End
      Begin VB.OptionButton Option2 
         Caption         =   "Level 2"
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   8.25
            Charset         =   161
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   120
         TabIndex        =   3
         Top             =   600
         Value           =   -1  'True
         Width           =   975
      End
      Begin VB.OptionButton Option1 
         Caption         =   "Level 1"
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   8.25
            Charset         =   161
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   255
         Left            =   120
         TabIndex        =   1
         Top             =   240
         Width           =   975
      End
      Begin VB.Label Label3 
         Caption         =   " [X,Y] Object Model Packed Format"
         Height          =   255
         Left            =   1080
         TabIndex        =   6
         Top             =   960
         Width           =   2535
      End
      Begin VB.Label Label2 
         Caption         =   " [X,Y,L] After flatten processing"
         Height          =   255
         Left            =   1080
         TabIndex        =   4
         Top             =   600
         Width           =   2535
      End
      Begin VB.Label Label1 
         Caption         =   " [ X,Y,Z,L] RAW Format"
         Height          =   255
         Left            =   1080
         TabIndex        =   2
         Top             =   240
         Width           =   2535
      End
   End
End
Attribute VB_Name = "frmBD_CMP"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
Public cMode As Byte

Private Sub Command1_Click()
    lastCMP_Mode = cMode
    Me.Hide
End Sub

Private Sub Form_Load()
    cMode = 2
    If lastCMP_Mode = 1 Then Option1.Value = True
    If lastCMP_Mode = 2 Then Option2.Value = True
    If lastCMP_Mode = 3 Then Option3.Value = True
End Sub

Private Sub Option1_Click()
    cMode = 1
End Sub

Private Sub Option2_Click()
    cMode = 2
End Sub

Private Sub Option3_Click()
    cMode = 3
End Sub
