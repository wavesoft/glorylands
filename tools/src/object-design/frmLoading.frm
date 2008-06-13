VERSION 5.00
Begin VB.Form frmLoading 
   BorderStyle     =   4  'Fixed ToolWindow
   Caption         =   "Tileset Manager"
   ClientHeight    =   795
   ClientLeft      =   45
   ClientTop       =   285
   ClientWidth     =   3855
   ControlBox      =   0   'False
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   795
   ScaleWidth      =   3855
   ShowInTaskbar   =   0   'False
   StartUpPosition =   2  'CenterScreen
   Begin VB.PictureBox Picture1 
      Align           =   2  'Align Bottom
      BackColor       =   &H8000000C&
      BorderStyle     =   0  'None
      Height          =   375
      Left            =   0
      ScaleHeight     =   375
      ScaleWidth      =   3855
      TabIndex        =   1
      Top             =   420
      Width           =   3855
      Begin VB.Label Label2 
         Alignment       =   2  'Center
         BackStyle       =   0  'Transparent
         Caption         =   "Label2"
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   8.25
            Charset         =   161
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         ForeColor       =   &H8000000E&
         Height          =   255
         Left            =   120
         TabIndex        =   2
         Top             =   60
         Width           =   3615
      End
      Begin VB.Label Label3 
         BackColor       =   &H00800000&
         Height          =   375
         Left            =   0
         TabIndex        =   3
         Top             =   0
         Width           =   15
      End
   End
   Begin VB.Label Label1 
      Caption         =   "Precaching tileset. This might take a while..."
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
      Left            =   120
      TabIndex        =   0
      Top             =   120
      Width           =   3615
   End
End
Attribute VB_Name = "frmLoading"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Public Sub SetWidth(ByVal Pos As Single, ByVal Of As Single)
    Label3.Width = Picture1.Width * Pos / Of
End Sub
