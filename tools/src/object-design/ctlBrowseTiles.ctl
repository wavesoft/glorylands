VERSION 5.00
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Begin VB.UserControl ctlBrowseTiles 
   ClientHeight    =   5370
   ClientLeft      =   0
   ClientTop       =   0
   ClientWidth     =   6030
   ScaleHeight     =   5370
   ScaleWidth      =   6030
   Begin MSComctlLib.ProgressBar ProgressBar1 
      Height          =   135
      Left            =   0
      TabIndex        =   4
      Top             =   300
      Visible         =   0   'False
      Width           =   3735
      _ExtentX        =   6588
      _ExtentY        =   238
      _Version        =   393216
      Appearance      =   0
   End
   Begin VB.PictureBox Picture2 
      AutoRedraw      =   -1  'True
      BackColor       =   &H00FFFFFF&
      BorderStyle     =   0  'None
      Height          =   375
      Left            =   4920
      ScaleHeight     =   375
      ScaleWidth      =   255
      TabIndex        =   3
      Top             =   480
      Visible         =   0   'False
      Width           =   255
   End
   Begin VB.Timer Timer1 
      Enabled         =   0   'False
      Interval        =   250
      Left            =   2400
      Top             =   2400
   End
   Begin VB.VScrollBar VScroll1 
      Height          =   3015
      LargeChange     =   10
      Left            =   3480
      Max             =   100
      TabIndex        =   2
      Top             =   360
      Width           =   255
   End
   Begin VB.PictureBox Picture1 
      AutoRedraw      =   -1  'True
      BackColor       =   &H00000000&
      BorderStyle     =   0  'None
      Height          =   3015
      Left            =   0
      ScaleHeight     =   3015
      ScaleWidth      =   3375
      TabIndex        =   1
      Top             =   360
      Width           =   3375
      Begin VB.Shape shpSelection 
         BorderColor     =   &H0000FF00&
         FillColor       =   &H8000000D&
         Height          =   495
         Left            =   0
         Top             =   0
         Width           =   495
      End
      Begin VB.Shape Shape1 
         BorderColor     =   &H000000FF&
         Height          =   495
         Left            =   0
         Top             =   0
         Width           =   495
      End
   End
   Begin VB.ComboBox Combo1 
      Height          =   315
      Left            =   0
      Style           =   2  'Dropdown List
      TabIndex        =   0
      Top             =   0
      Width           =   3735
   End
End
Attribute VB_Name = "ctlBrowseTiles"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

Dim Mapping As Collection, MapLoc As Collection
Dim MaxY As Integer, MaxFitY As Integer
Dim Categories() As String

Dim MouseX As Integer, MouseY As Integer
Dim SelX As Integer, SelY As Integer, SelW As Integer, SelH As Integer
Dim lBlitY As Integer

Public Event CategoryChange(NewName As String)
Public Event SelectionChange(Category As String, SelX As Integer, SelY As Integer, SelW As Integer, SelH As Integer, TileBmp() As StdPicture, TileName() As String, BlockImg As StdPicture)

Dim CArc As New clsImgArchive
Dim CurBasepath As String

' Returns the first (size)-2 parts of the array joined with specified glue
Private Function getBaseName(parts() As String, Optional Glue As String = "-") As String
    Dim s As String, i
    For i = 0 To UBound(parts) - 2
        If s <> "" Then s = s & Glue
        s = s & parts(i)
    Next i
    getBaseName = s
End Function

Private Sub LoadCategories()
    Dim s As String, i As Integer, f() As String, s2 As String
    
    ' Detect all the types (Note! First item XXX-0-0.gif MUST exist!)
    s = Dir(BasePath & "*.ts")
    i = 0
    
    ' Load category basename into the array and display it's name on the combo box
    Do While s <> ""
        ReDim Preserve Categories(i)
        f = Split(s, ".")
        Categories(i) = f(0)
        f(0) = StrConv(Replace(Categories(i), "-", " "), vbProperCase)
        Combo1.AddItem Left(s, Len(s) - 3)
        i = i + 1
        s = Dir
    Loop
    
    InitCache i
End Sub

Private Function Exists(Filename As String) As Boolean
    Exists = (Dir(Filename) <> "")
End Function

Private Sub LoadTiles(catID As Integer)
    On Error Resume Next
    Dim s As String, Found As Boolean, X, Y, i
    Dim f() As String
    Dim Tile(1)
    
    CurBasepath = Categories(catID) & "-"
    ' Attempt to load from cache
    'If Not TileCache(catID) Is Nothing Then
    '    Set Mapping = TileCache(catID)
    '    MaxY = TileMaxY(catID)
    '    Set MapLoc = TileLocation(catID)
    '    Exit Sub
    'End If
    
    ' Initialize
    i = 0
    Y = 0
    
    ' Reset information
    Set Mapping = New Collection
    Set MapLoc = New Collection
    
    ' Splash
    'Load frmLoading
    'frmLoading.Refresh
    'frmLoading.Show
    'frmLoading.ZOrder
    'DoEvents
    
    ' Dir & Read
    's = Dir(BasePath & Categories(catID) & "*.gif")
    'MaxY = 0
    'Do While s <> ""
    '    ' Parse info
    '    f = Split(s, "-")
    '    X = f(UBound(f) - 1)
    '    f = Split(f(UBound(f)), ".", 2)
    '    Y = f(0)
    '    If Val(Y) > MaxY Then MaxY = Val(Y)
    '
    '    ' Store tile info
    '    Tile(0) = s
    '    Set Tile(1) = LoadPicture(BasePath & s)
    '
    '    ' Insert mapping coordinates
    '    Mapping.Add Tile, X & "," & Y
    '    MapLoc.Add X & "," & Y
    '    frmLoading.Label2.Caption = Tile(0)
    '    DoEvents
    '
    '    s = Dir
    'Loop
    
    Dim iLen As Integer
    iLen = CArc.FileLength(Categories(catID))
    For Y = 0 To iLen - 1
        For X = 0 To 7
            ' Store tile info
            s = Categories(catID) & "-" & X & "-" & Y
            Tile(0) = s
            'Set Tile(1) = imgCache.Image(s)
        
            ' Insert mapping coordinates
            Mapping.Add Tile, X & "," & Y
            MapLoc.Add X & "," & Y
            'frmLoading.Label2.Caption = Tile(0)
        Next X
        'frmLoading.SetWidth Y + 1, iLen
        'DoEvents
    Next Y
    
    
    ' Unsplash...
    'Unload frmLoading
    
    ' Store to cache
    'Set TileCache(catID) = Mapping
    MaxY = iLen
    TileMaxY(catID) = iLen
    Set TileLocation(catID) = MapLoc
End Sub

Private Sub DisplayTiles(StartLine As Integer)
    On Error Resume Next
    Dim Y, X
    If lBlitY = StartLine Then Exit Sub
    Picture1.Cls
    ProgressBar1.Max = MaxFitY * 8
    ProgressBar1.Value = 0
    ProgressBar1.Visible = True
    DoEvents
    For Y = StartLine To StartLine + MaxFitY
        For X = 0 To 7
            'Picture1.PaintPicture Mapping(X & "," & Y)(1), X * 33 + 1, (Y - StartLine) * 33 + 1
            Picture1.PaintPicture imgStorage.LoadImage(CurBasepath & X & "-" & Y), X * 33 + 1, (Y - StartLine) * 33 + 1
            ProgressBar1.Value = ProgressBar1.Value + 1
        Next X
        DoEvents
    Next Y
    lBlitY = StartLine
    ProgressBar1.Visible = False
End Sub

Private Function LocationExists(ByVal X As Integer, ByVal Y As Integer) As Boolean
    Dim i
    Dim s As String
    For i = 1 To MapLoc.Count
        s = X & "," & Y
        If MapLoc(i) = s Then
            LocationExists = True
            Exit Function
        End If
    Next i
    LocationExists = False
End Function

Private Sub Combo1_Click()
    imgCache.Clear
    LoadTiles Combo1.ListIndex
    If MaxY - MaxFitY < 0 Then
        VScroll1.Enabled = False
    Else
        VScroll1.Enabled = True
        VScroll1.Max = MaxY - MaxFitY
    End If
    VScroll1.Value = 0
    lBlitY = -1
    DisplayTiles 0
End Sub

Private Sub Picture1_MouseDown(Button As Integer, Shift As Integer, X As Single, Y As Single)
    SelX = MouseX
    SelY = MouseY + VScroll1.Value
    SelW = 1
    SelH = 1
    DisplaySel
    Timer1.Enabled = False
    shpSelection.Visible = True
End Sub

Private Sub Picture1_MouseMove(Button As Integer, Shift As Integer, X As Single, Y As Single)
    MouseX = X \ 33
    MouseY = Y \ 33
    Shape1.Move MouseX * 33, MouseY * 33
    
    If Button = 1 Then
        If MouseX < SelX Then SelX = MouseX
        If MouseY + VScroll1.Value < SelY Then SelY = MouseY + VScroll1.Value
        SelW = MouseX - SelX + 1
        SelH = MouseY + VScroll1.Value - SelY + 1
        DisplaySel
    End If
End Sub

Private Sub Picture1_MouseUp(Button As Integer, Shift As Integer, X As Single, Y As Single)
    Timer1.Enabled = True
    
    Dim iName() As String, iImg() As StdPicture, X2, Y2, T
    ReDim iName(SelW - 1, SelH - 1)
    ReDim iImg(SelW - 1, SelH - 1)

    Picture2.Width = 32 * SelW * Screen.TwipsPerPixelX
    Picture2.Height = 32 * SelH * Screen.TwipsPerPixelY
    DoEvents

    Picture2.Cls
    For X2 = 0 To SelW - 1
        For Y2 = 0 To SelH - 1
            If (LocationExists((X2 + SelX), (Y2 + SelY))) Then
                T = Mapping((X2 + SelX) & "," & (Y2 + SelY))
                Set T(1) = imgStorage.LoadImage(T(0))
                iName(X2, Y2) = T(0)
                Set iImg(X2, Y2) = T(1)
                If Not iImg(X2, Y2) Is Nothing Then Picture2.PaintPicture iImg(X2, Y2), X2 * 32, Y2 * 32
            End If
        Next Y2
    Next X2

    RaiseEvent SelectionChange(Categories(Combo1.ListIndex), SelX, SelY, SelW, SelH, iImg, iName, Picture2.Image)
End Sub

Private Sub Timer1_Timer()
    shpSelection.Visible = Not shpSelection.Visible
End Sub

Private Sub DisplaySel()
    shpSelection.Left = SelX * 33
    shpSelection.Top = (SelY - VScroll1.Value) * 33
    shpSelection.Width = 33 * SelW
    shpSelection.Height = 33 * SelH
End Sub

Public Sub Init()
    
    ' Initalize graphics
    '                8 tiles x 32px + 33 x 1px lines
    Picture1.Width = ((33 * 8) + 1) * Screen.TwipsPerPixelX
    Picture1.ScaleMode = vbPixels
    Shape1.Width = 34
    Shape1.Height = 34
    shpSelection.Width = 34
    shpSelection.Height = 34
    Picture2.ScaleMode = vbPixels
    
    ' Initialize script
    ReDim Categories(0)
    LoadCategories
    If Combo1.ListCount > 0 Then Combo1.ListIndex = 0
    
    ' Start blinking
    Timer1.Enabled = True
End Sub

Private Sub UserControl_Resize()
    ' Force width
    UserControl.Width = Picture1.Width + VScroll1.Width
    
    ' Resize components
    Combo1.Width = UserControl.Width
    Picture1.Move 0, Combo1.Height, Picture1.Width, UserControl.Height - Combo1.Height
    VScroll1.Height = Picture1.Height
    VScroll1.Top = Combo1.Height
    VScroll1.Left = UserControl.Width - VScroll1.Width
    ProgressBar1.Width = UserControl.Width - VScroll1.Width
    
    ' Calculate maximum y tiles that fits in our view
    MaxFitY = Fix((UserControl.Height - Combo1.Height - 2) / (33 * Screen.TwipsPerPixelX)) - 1
End Sub

Private Sub VScroll1_Change()
    DoEvents
    DisplayTiles VScroll1.Value
    DisplaySel
End Sub

Private Sub VScroll1_Scroll()
    'DoEvents
    'VScroll1_Change
End Sub

