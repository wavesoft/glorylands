VERSION 5.00
Begin VB.Form frmMapInfo 
   BorderStyle     =   3  'Fixed Dialog
   Caption         =   "Map Information"
   ClientHeight    =   2145
   ClientLeft      =   45
   ClientTop       =   330
   ClientWidth     =   6120
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
   ScaleHeight     =   2145
   ScaleWidth      =   6120
   ShowInTaskbar   =   0   'False
   StartUpPosition =   2  'CenterScreen
   Begin VB.CommandButton Command3 
      Caption         =   "Grid Structure"
      Height          =   375
      Left            =   120
      TabIndex        =   11
      Top             =   1680
      Width           =   1455
   End
   Begin VB.CommandButton Command2 
      Caption         =   "Close"
      Default         =   -1  'True
      Height          =   375
      Left            =   4800
      TabIndex        =   10
      Top             =   1680
      Width           =   1215
   End
   Begin VB.Frame Frame2 
      Caption         =   "Appearence"
      Height          =   1455
      Left            =   3120
      TabIndex        =   7
      Top             =   120
      Width           =   2895
      Begin VB.CommandButton Command1 
         Caption         =   "Change"
         Height          =   375
         Left            =   1920
         TabIndex        =   9
         Top             =   300
         Width           =   855
      End
      Begin VB.Image Image1 
         BorderStyle     =   1  'Fixed Single
         Height          =   540
         Left            =   1320
         Picture         =   "frmMapInfo.frx":0000
         Top             =   240
         Width           =   540
      End
      Begin VB.Label Label7 
         Caption         =   "Background :"
         Height          =   255
         Left            =   120
         TabIndex        =   8
         Top             =   360
         Width           =   1095
      End
   End
   Begin VB.Frame Frame1 
      Caption         =   "Statistics"
      Height          =   1455
      Left            =   120
      TabIndex        =   0
      Top             =   120
      Width           =   2895
      Begin VB.Label Label6 
         BorderStyle     =   1  'Fixed Single
         Caption         =   " 0"
         Height          =   255
         Left            =   2040
         TabIndex        =   6
         Top             =   960
         Width           =   615
      End
      Begin VB.Label Label5 
         Caption         =   "Maximum Layers Used :"
         Height          =   255
         Left            =   120
         TabIndex        =   5
         Top             =   960
         Width           =   1935
      End
      Begin VB.Label Label4 
         BorderStyle     =   1  'Fixed Single
         Caption         =   " 0"
         Height          =   255
         Left            =   2040
         TabIndex        =   4
         Top             =   600
         Width           =   615
      End
      Begin VB.Label Label3 
         Caption         =   "Current Height :"
         Height          =   255
         Left            =   120
         TabIndex        =   3
         Top             =   600
         Width           =   1335
      End
      Begin VB.Label Label2 
         BorderStyle     =   1  'Fixed Single
         Caption         =   " 0"
         Height          =   255
         Left            =   2040
         TabIndex        =   2
         Top             =   240
         Width           =   615
      End
      Begin VB.Label Label1 
         Caption         =   "Current Width :"
         Height          =   255
         Left            =   120
         TabIndex        =   1
         Top             =   240
         Width           =   1335
      End
   End
End
Attribute VB_Name = "frmMapInfo"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
Dim hMap As frmMap

Public Sub Display(hForm As frmMap)
    Set hMap = hForm
    Label2.Caption = " " & hForm.Designer.MaxWidth
    Label4.Caption = " " & hForm.Designer.MaxHeight
    Label6.Caption = " ---"
    Set Image1.Picture = ImgLib.Image(hMap.Designer.BackgroundImage)
    Me.Show 1
End Sub

Private Sub Command1_Click()
    frmSelectTile.Display hMap.Designer.BackgroundImage
    If frmSelectTile.FileName <> "" Then
        Set Image1.Picture = ImgLib.Image(frmSelectTile.FileName)
        hMap.SetBackground frmSelectTile.FileName
    End If
    Unload frmSelectTile
End Sub

Private Sub Command2_Click()
    Unload Me
End Sub

Private Sub Command3_Click()
    Load frmStructView
    frmStructView.Text1.Text = hMap.Designer.AttGrid.StructPrint
    frmStructView.Show 1
End Sub

