VERSION 5.00
Begin VB.Form frmPreview 
   BackColor       =   &H00FFFFFF&
   BorderStyle     =   5  'Sizable ToolWindow
   Caption         =   "Object Preview"
   ClientHeight    =   3195
   ClientLeft      =   165
   ClientTop       =   405
   ClientWidth     =   4680
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   3195
   ScaleWidth      =   4680
   ShowInTaskbar   =   0   'False
   StartUpPosition =   2  'CenterScreen
   Begin VB.PictureBox picBlit 
      Appearance      =   0  'Flat
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      ForeColor       =   &H80000008&
      Height          =   615
      Left            =   720
      ScaleHeight     =   615
      ScaleWidth      =   2040
      TabIndex        =   0
      Top             =   600
      Visible         =   0   'False
      Width           =   2040
   End
   Begin VB.Image img 
      Height          =   495
      Index           =   0
      Left            =   1800
      Top             =   1320
      Width           =   1215
   End
   Begin VB.Menu mnupop 
      Caption         =   "mnupop"
      Visible         =   0   'False
      Begin VB.Menu mnuMode 
         Caption         =   "No stretch"
         Checked         =   -1  'True
         Index           =   0
      End
      Begin VB.Menu mnuMode 
         Caption         =   "Stretch center"
         Index           =   1
      End
      Begin VB.Menu mnuMode 
         Caption         =   "Horizontal Side Stretch"
         Index           =   2
      End
      Begin VB.Menu mnuMode 
         Caption         =   "Vertical Side Stretch"
         Index           =   3
      End
      Begin VB.Menu mnuMode 
         Caption         =   "Stretch Sides"
         Index           =   4
      End
      Begin VB.Menu mnuLine1 
         Caption         =   "-"
      End
      Begin VB.Menu mnuRndCenter 
         Caption         =   "Randomize Center"
      End
   End
End
Attribute VB_Name = "frmPreview"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
Private Declare Function BitBlt Lib "gdi32" (ByVal hDestDC As Long, ByVal x As Long, ByVal y As Long, ByVal nWidth As Long, ByVal nHeight As Long, ByVal hSrcDC As Long, ByVal xSrc As Long, ByVal ySrc As Long, ByVal dwRop As Long) As Long
Private Declare Function GetWindowDC Lib "user32" (ByVal hwnd As Long) As Long
Private Declare Function ReleaseDC Lib "user32" (ByVal hwnd As Long, ByVal hdc As Long) As Long
Private Const SRCCOPY = &HCC0020 ' (DWORD) dest = source

Private Declare Function GetWindowRect Lib "user32" (ByVal hwnd As Long, lpRect As RECT) As Long
Private Declare Function GetWindowPlacement Lib "user32" (ByVal hwnd As Long, lpwndpl As WINDOWPLACEMENT) As Long
Private Type RECT
        Left As Long
        Top As Long
        Right As Long
        Bottom As Long
End Type
Private Type POINTAPI
        x As Long
        y As Long
End Type
Private Type WINDOWPLACEMENT
        Length As Long
        flags As Long
        showCmd As Long
        ptMinPosition As POINTAPI
        ptMaxPosition As POINTAPI
        rcNormalPosition As RECT
End Type


Dim WithEvents ImgGrid As clsImgGridObj
Attribute ImgGrid.VB_VarHelpID = -1
Dim LW As Integer, lH As Integer

Private Enum BlitMode
    bltOneToOne = 1
    bltCenterStretch = 2
    bltHSideStretch = 3
    bltVSideStretch = 4
    bltBothSideStretch = 5
End Enum

Dim cMode As BlitMode
Dim cRandomizedCenter As Boolean

Dim LockResize As Boolean

Dim MaxW As Integer, MaxH As Integer
Dim MaxTopH As Integer
Dim MaxBotH As Integer
Dim MaxMidH As Integer
Dim MaxLeftW As Integer
Dim MaxRightW As Integer
Dim MaxMidW As Integer
        
Dim BackBlit As Boolean

Public Function GetPreview() As StdPicture
    cMode = bltOneToOne
    LockResize = True
    
    ' Calculate maximum ranges
    CalcMaxRange Grids
    
    ' Resize
    Me.Height = MaxH * Screen.TwipsPerPixelY * 32 + 360
    Me.Width = MaxW * Screen.TwipsPerPixelX * 32 + 120
    
    ' Blit the grid
    Dim cW As Integer, cH As Integer
    cW = MaxW \ 32
    cH = MaxH \ 32
    BlitGrid 0, 0, cW, cH, Grids, bltOneToOne
    
    ' Transfer DC to picture box
    Dim wDC As Long
    Me.Show
    DoEvents
    wDC = GetWindowDC(hwnd)
    
    picBlit.Width = (Me.Width - 120) / Screen.TwipsPerPixelX
    picBlit.Height = (Me.Height - 360) / Screen.TwipsPerPixelY
    
    picBlit.AutoRedraw = True
    picBlit.Cls
    BitBlt picBlit.hdc, -5, -20, Me.Width / Screen.TwipsPerPixelX, Me.Height / Screen.TwipsPerPixelX, wDC, 0, 0, SRCCOPY
    picBlit.Refresh
    
    ' Cleanup
    ReleaseDC hwnd, wDC
    
    ' Return the image
    Set GetPreview = picBlit.Image
        
    Me.Hide
    DoEvents
    LockResize = False
End Function

Private Sub Form_Load()
    Set ImgGrid = New clsImgGridObj
    Me.ScaleMode = vbPixels
    Me.Width = 32 * 10 * Screen.TwipsPerPixelX
    Me.Height = 32 * 10 * Screen.TwipsPerPixelY
    cMode = bltOneToOne
End Sub

Private Sub RangeBlit(ByVal PosX As Integer, ByVal PosY As Integer, ByVal RangeW As Integer, ByVal RangeH As Integer, Grid As TileGrid, Optional Randomized As Boolean = False)
    Dim x, y, l, oX, oY
    oX = 0
    oY = 0
    For l = 0 To 3
        For y = PosY To PosY + RangeH - 1
            If Not Randomized Then oX = 0
            For x = PosX To PosX + RangeW - 1
                
                
                If Randomized Then
                    oX = Fix(Rnd * Grid.ActualW)
                    oY = Fix(Rnd * Grid.ActualH)
                End If
                
                ImgGrid.PenAction x, y, l, Grid.Grid(l, oX, oY)
                
                If Not Randomized Then
                    oX = oX + 1
                    If (oX >= Grid.ActualW) Then oX = 0
                End If
            Next x
            If Not Randomized Then
                oY = oY + 1
                If (oY >= Grid.ActualH) Then oY = 0
            End If
        Next y
    Next l
End Sub

Private Sub CalcMaxRange(ObjData() As TileGrid)
    Dim i

    MaxW = 0
    MaxH = 0
    MaxTopH = 0
    MaxBotH = 0
    MaxMidH = 0
    MaxLeftW = 0
    MaxRightW = 0
    MaxMidW = 0

    For i = 0 To 2  ' 0,1,2
        If ObjData(i).ActualH > MaxTopH Then MaxTopH = ObjData(i).ActualH
    Next i
    For i = 3 To 5  ' 3,4,5
        If ObjData(i).ActualH > MaxMidH Then MaxMidH = ObjData(i).ActualH
    Next i
    For i = 6 To 8  ' 6,7,8
        If ObjData(i).ActualH > MaxBotH Then MaxBotH = ObjData(i).ActualH
    Next i
    For i = 0 To 6 Step 3  ' 0,3,6
        If ObjData(i).ActualW > MaxLeftW Then MaxLeftW = ObjData(i).ActualW
    Next i
    For i = 1 To 7 Step 3  ' 1,4,7
        If ObjData(i).ActualW > MaxMidW Then MaxMidW = ObjData(i).ActualW
    Next i
    For i = 2 To 8 Step 3  ' 2,5,8
        If ObjData(i).ActualW > MaxRightW Then MaxRightW = ObjData(i).ActualW
    Next i
        
    MaxW = MaxLeftW + MaxMidW + MaxRightW
    MaxH = MaxTopH + MaxMidH + MaxBotH

End Sub

Private Sub BlitGrid(ByVal PosX As Integer, ByVal PosY As Integer, ByVal RangeW As Integer, ByVal RangeH As Integer, ObjData() As TileGrid, Optional Mode As BlitMode = bltCenterStretch)
    ImgGrid.Truncate
    
    ' 1) Calculate the maximum height/widths
    CalcMaxRange ObjData
    
    ' 2) Resize the middle widths based on our real widths
    Dim tV As Integer
    If Mode = bltCenterStretch Then
        MaxMidW = RangeW - MaxLeftW - MaxRightW
        MaxMidH = RangeH - MaxTopH - MaxBotH
    ElseIf Mode = bltBothSideStretch Then
        MaxLeftW = (RangeW - MaxMidW) / 2
        MaxRightW = MaxLeftW
        MaxTopH = (RangeH - MaxMidH) / 2
        MaxBotH = MaxTopH
    ElseIf Mode = bltHSideStretch Then
        MaxLeftW = (RangeW - MaxMidW) / 2
        MaxRightW = MaxLeftW
        MaxMidH = RangeH - MaxTopH - MaxBotH
    ElseIf Mode = bltVSideStretch Then
        MaxMidW = RangeW - MaxLeftW - MaxRightW
        MaxTopH = (RangeH - MaxMidH) / 2
        MaxBotH = MaxTopH
    End If
    
    ' 3) Blit the grids
    RangeBlit 0, 0, MaxLeftW, MaxTopH, ObjData(0)
    RangeBlit MaxLeftW, 0, MaxMidW, MaxTopH, ObjData(1)
    RangeBlit MaxLeftW + MaxMidW, 0, MaxRightW, MaxTopH, ObjData(2)
    
    RangeBlit 0, MaxTopH, MaxLeftW, MaxMidH, ObjData(3)
    RangeBlit MaxLeftW, MaxTopH, MaxMidW, MaxMidH, ObjData(4), cRandomizedCenter
    RangeBlit MaxLeftW + MaxMidW, MaxTopH, MaxRightW, MaxMidH, ObjData(5)
    
    RangeBlit 0, MaxTopH + MaxMidH, MaxLeftW, MaxBotH, ObjData(6)
    RangeBlit MaxLeftW, MaxTopH + MaxMidH, MaxMidW, MaxBotH, ObjData(7)
    RangeBlit MaxLeftW + MaxMidW, MaxTopH + MaxMidH, MaxRightW, MaxBotH, ObjData(8)
End Sub

Private Sub Form_MouseUp(Button As Integer, Shift As Integer, x As Single, y As Single)
    If Button = 2 Then PopupMenu mnupop, vbPopupMenuLeftAlign, x, y
End Sub

Private Sub Form_QueryUnload(Cancel As Integer, UnloadMode As Integer)
    Set ImgGrid = Nothing
    Me.Hide
    Dim i
    On Error Resume Next
    For i = img.LBound To img.UBound
        Unload img(i)
    Next i
    Unload Me
End Sub

Private Sub Form_Resize()
    If LockResize Then Exit Sub
    ' Paint the horizontal images
    Dim cW As Integer, cH As Integer
    cW = Me.Width \ (32 * Screen.TwipsPerPixelX)
    cH = Me.Height \ (32 * Screen.TwipsPerPixelY) - 1
    If cW <> LW Or cH <> lH Then
        BlitGrid 0, 0, cW, cH, Grids, cMode
        LW = cW
        lH = cH
    End If
End Sub

Private Sub img_MouseUp(Index As Integer, Button As Integer, Shift As Integer, x As Single, y As Single)
    Form_MouseUp Button, Shift, (x / Screen.TwipsPerPixelX) + img(Index).Left, (y / Screen.TwipsPerPixelY) + img(Index).Top
End Sub

Private Sub ImgGrid_AllocateImage(x As Integer, y As Integer, Image As stdole.StdPicture, IndexID As Integer)
    Dim i
    i = img.UBound + 1
    Load img(i)
    img(i).Left = x
    img(i).Top = y
    img(i).Visible = True
    Set img(i).Picture = Image
    IndexID = i
End Sub

Private Sub ImgGrid_AlterImage(Index As Integer, Image As stdole.StdPicture)
    Set img(Index).Picture = Image
End Sub

Private Sub ImgGrid_DestroyImage(Index As Integer)
    'Set img(Index).Container = Nothing
    On Error Resume Next
    img(Index).Visible = False
    Unload img(Index)
End Sub

Private Sub ImgGrid_ZOrderBack(Image As Integer)
    img(Image).ZOrder 1
End Sub

Private Sub mnuMode_Click(Index As Integer)
    cMode = Index + 1
    LW = -1
    lH = -1
    Form_Resize
    Dim i
    For i = 0 To mnuMode.Count - 1
        mnuMode(i).Checked = (Index = i)
    Next i
End Sub

Private Sub mnuRndCenter_Click()
    mnuRndCenter.Checked = Not mnuRndCenter.Checked
    cRandomizedCenter = mnuRndCenter.Checked
    LW = 0
    Form_Resize
End Sub
