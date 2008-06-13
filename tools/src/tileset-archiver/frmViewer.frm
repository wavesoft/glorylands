VERSION 5.00
Begin VB.Form Form1 
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Tileset Viewer"
   ClientHeight    =   5025
   ClientLeft      =   45
   ClientTop       =   330
   ClientWidth     =   7110
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   5025
   ScaleWidth      =   7110
   StartUpPosition =   3  'Windows Default
   Begin VB.CommandButton Command2 
      Caption         =   "<< Previous Page"
      Height          =   375
      Left            =   3000
      TabIndex        =   4
      Top             =   4440
      Width           =   1695
   End
   Begin VB.CommandButton Command1 
      Caption         =   "Next Page >>"
      Height          =   375
      Left            =   5280
      TabIndex        =   3
      Top             =   4440
      Width           =   1695
   End
   Begin VB.PictureBox Picture1 
      AutoRedraw      =   -1  'True
      BackColor       =   &H00000000&
      Height          =   3975
      Left            =   3000
      ScaleHeight     =   261
      ScaleMode       =   3  'Pixel
      ScaleWidth      =   261
      TabIndex        =   2
      Top             =   360
      Width           =   3975
   End
   Begin VB.ListBox List1 
      Height          =   4545
      Left            =   120
      TabIndex        =   0
      Top             =   360
      Width           =   2655
   End
   Begin VB.Label Label1 
      Caption         =   "Archives on current dir:"
      Height          =   255
      Left            =   120
      TabIndex        =   1
      Top             =   120
      Width           =   2055
   End
End
Attribute VB_Name = "Form1"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
Dim cPage As Integer

Private Sub Command1_Click()
    cPage = cPage + 8
    Blit
End Sub

Private Sub Command2_Click()
    cPage = cPage - 8
    If cPage < 0 Then cPage = 0
    Blit
End Sub

Private Sub Form_Load()
    Dim s As String
    s = Dir(FixPath(App.Path) & "*.ts")
    While s <> ""
        List1.AddItem Left(s, Len(s) - 3)
        s = Dir
    Wend
End Sub

Private Sub Blit()
    Dim x, y, iPic As StdPicture, bF As String
    Dim C As New clsReader
    
    bF = List1.List(List1.ListIndex) & "-"
    
    Picture1.Cls
    For y = cPage To cPage + 7
        For x = 0 To 7
            Set iPic = C.LoadImage(bF & x & "-" & y)
                    
            If Not iPic Is Nothing Then Picture1.PaintPicture iPic, x * 33, (y - cPage) * 33
        Next x
    Next y
End Sub

Private Sub List1_Click()
    cPage = 0
    Blit
End Sub
