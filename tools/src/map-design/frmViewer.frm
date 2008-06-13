VERSION 5.00
Begin VB.Form frmSelectTile 
   BorderStyle     =   3  'Fixed Dialog
   Caption         =   "Select Tile"
   ClientHeight    =   4695
   ClientLeft      =   45
   ClientTop       =   330
   ClientWidth     =   7110
   ControlBox      =   0   'False
   BeginProperty Font 
      Name            =   "Tahoma"
      Size            =   8.25
      Charset         =   0
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   4695
   ScaleWidth      =   7110
   ShowInTaskbar   =   0   'False
   StartUpPosition =   2  'CenterScreen
   Begin VB.CommandButton Command3 
      Cancel          =   -1  'True
      Caption         =   "&Cancel"
      Height          =   375
      Left            =   4080
      TabIndex        =   4
      Top             =   4200
      Width           =   1815
   End
   Begin VB.CommandButton Command2 
      Caption         =   "<<"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   375
      Left            =   3000
      TabIndex        =   3
      Top             =   4200
      Width           =   975
   End
   Begin VB.CommandButton Command1 
      Caption         =   ">>"
      Height          =   375
      Left            =   6000
      TabIndex        =   2
      Top             =   4200
      Width           =   975
   End
   Begin VB.PictureBox Picture1 
      AutoRedraw      =   -1  'True
      BackColor       =   &H00000000&
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
      Left            =   3000
      ScaleHeight     =   261
      ScaleMode       =   3  'Pixel
      ScaleWidth      =   261
      TabIndex        =   1
      Top             =   120
      Width           =   3975
      Begin VB.Shape Shape1 
         BorderColor     =   &H0000FF00&
         Height          =   1095
         Left            =   360
         Top             =   360
         Width           =   975
      End
   End
   Begin VB.ListBox List1 
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   4350
      Left            =   120
      TabIndex        =   0
      Top             =   120
      Width           =   2655
   End
End
Attribute VB_Name = "frmSelectTile"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
Dim cPage As Integer
Public FileName As String
Dim Locked As Boolean
Dim cLength As Integer

Private Sub Command1_Click()
    cPage = cPage + 8
    If cPage + 8 > cLength Then cPage = cLength - 8
    Blit
End Sub

Private Sub Command2_Click()
    cPage = cPage - 8
    If cPage < 0 Then cPage = 0
    Blit
End Sub

Private Sub Command3_Click()
    FileName = ""
    Me.Hide
End Sub

Private Sub Form_Load()
    Dim s As String
    s = Dir(FixPath(App.Path) & "tilesets\*.ts")
    While s <> ""
        List1.AddItem Left(s, Len(s) - 3)
        s = Dir
    Wend
    Shape1.Move 0, 0, 32, 32
    If List1.ListCount > 0 Then
        List1.ListIndex = 0
        List1.Enabled = True
        Command1.Enabled = True
        Command2.Enabled = True
    Else
        List1.AddItem "(No tilesets found)"
        List1.FontItalic = True
        List1.Enabled = False
        Picture1.Enabled = False
        Command1.Enabled = False
        Command2.Enabled = False
    End If
End Sub

Public Sub Display(Name As String)
    Dim f() As String
    Dim Y As String
    Dim X As String
    Dim Map As String
    Dim i
    f = Split(Name, "-")
    If UBound(f) < 2 Then
        Map = Name
        X = "0"
        Y = "0"
    Else
        For i = 0 To UBound(f) - 2
            If Map <> "" Then Map = Map & "-"
            Map = Map & f(i)
            X = f(i + 1)
            Y = f(i + 2)
        Next i
    End If
    
    For i = 0 To List1.ListCount - 1
        If LCase(Trim(List1.List(i))) = LCase(Trim(Map)) Then
            Locked = True
            List1.ListIndex = i
            cPage = Y
            Blit
            Locked = False
            Shape1.Left = X * 33
            Exit For
        End If
    Next i
    
    Me.Show 1
End Sub

Private Sub Blit()
    Dim X, Y, iPic As StdPicture, bF As String
    Dim C As New clsImgArchive
    
    bF = List1.List(List1.ListIndex) & "-"
    
    Picture1.Cls
    For Y = cPage To cPage + 7
        For X = 0 To 7
            Set iPic = C.LoadImage(bF & X & "-" & Y)
            cLength = C.ImageLength
            If Not iPic Is Nothing Then Picture1.PaintPicture iPic, X * 33, (Y - cPage) * 33
        Next X
    Next Y
    
    ' We do not have any presistent files open, so close all handles every time we are done blitting the grid
    Close
End Sub

Private Sub List1_Click()
    If Locked Then Exit Sub
    cPage = 0
    Blit
End Sub

Private Sub Picture1_MouseMove(Button As Integer, Shift As Integer, X As Single, Y As Single)
    Dim iX As Integer
    Dim iY As Integer
    iX = X \ 33
    iY = Y \ 33
    Shape1.Left = iX * 33
    Shape1.Top = iY * 33
End Sub

Private Sub Picture1_MouseUp(Button As Integer, Shift As Integer, X As Single, Y As Single)
    Dim iX As Integer
    Dim iY As Integer
    iX = X \ 33
    iY = Y \ 33 + cPage
    FileName = List1.List(List1.ListIndex) & "-" & iX & "-" & iY
    Me.Hide
End Sub
