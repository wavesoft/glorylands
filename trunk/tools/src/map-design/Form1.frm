VERSION 5.00
Object = "{F9043C88-F6F2-101A-A3C9-08002B2F49FB}#1.2#0"; "comdlg32.ocx"
Begin VB.Form frmMap 
   AutoRedraw      =   -1  'True
   BackColor       =   &H00FFFFFF&
   Caption         =   "Form1"
   ClientHeight    =   5100
   ClientLeft      =   60
   ClientTop       =   630
   ClientWidth     =   8160
   KeyPreview      =   -1  'True
   LinkTopic       =   "Form1"
   MDIChild        =   -1  'True
   MouseIcon       =   "Form1.frx":0000
   MousePointer    =   99  'Custom
   NegotiateMenus  =   0   'False
   ScaleHeight     =   340
   ScaleMode       =   3  'Pixel
   ScaleWidth      =   544
   WindowState     =   2  'Maximized
   Begin MSComDlg.CommonDialog CommonDialog1 
      Left            =   3840
      Top             =   2280
      _ExtentX        =   847
      _ExtentY        =   847
      _Version        =   393216
      CancelError     =   -1  'True
   End
   Begin VB.Shape shpSelection 
      BorderColor     =   &H0000C000&
      BorderWidth     =   2
      Height          =   495
      Left            =   3480
      Top             =   2280
      Visible         =   0   'False
      Width           =   1215
   End
   Begin VB.Shape shpOrigin 
      BorderColor     =   &H000080FF&
      BorderWidth     =   3
      Height          =   495
      Left            =   4560
      Top             =   2280
      Visible         =   0   'False
      Width           =   1215
   End
   Begin VB.Image Ico 
      Height          =   480
      Index           =   5
      Left            =   6480
      Picture         =   "Form1.frx":0212
      Top             =   1080
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.Image Ico 
      Height          =   480
      Index           =   4
      Left            =   6000
      Picture         =   "Form1.frx":0364
      Top             =   1080
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.Image Ico 
      Height          =   480
      Index           =   3
      Left            =   5760
      Picture         =   "Form1.frx":04B6
      Top             =   1080
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.Image Ico 
      Height          =   480
      Index           =   2
      Left            =   5400
      Picture         =   "Form1.frx":06C8
      Top             =   1080
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.Image Ico 
      Height          =   480
      Index           =   1
      Left            =   5040
      Picture         =   "Form1.frx":081A
      Top             =   1080
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.Image Ico 
      Height          =   480
      Index           =   0
      Left            =   4680
      Picture         =   "Form1.frx":096C
      Top             =   1080
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.Label lblAtt 
      Alignment       =   2  'Center
      BackColor       =   &H000000FF&
      BackStyle       =   0  'Transparent
      Caption         =   "0"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   18
         Charset         =   161
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   480
      Index           =   0
      Left            =   0
      TabIndex        =   0
      Top             =   0
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.Line lnZRef 
      BorderWidth     =   2
      DrawMode        =   6  'Mask Pen Not
      Visible         =   0   'False
      X1              =   16
      X2              =   504
      Y1              =   256
      Y2              =   256
   End
   Begin VB.Image Image1 
      Height          =   480
      Left            =   2280
      Picture         =   "Form1.frx":0ABE
      Top             =   2880
      Visible         =   0   'False
      Width           =   480
   End
   Begin VB.Shape shpHover 
      BorderColor     =   &H0000FFFF&
      BorderWidth     =   2
      DrawMode        =   6  'Mask Pen Not
      FillStyle       =   4  'Upward Diagonal
      Height          =   375
      Left            =   480
      Top             =   1200
      Visible         =   0   'False
      Width           =   1095
   End
   Begin VB.Image Img 
      Height          =   495
      Index           =   0
      Left            =   3480
      Top             =   2280
      Width           =   1215
   End
   Begin VB.Menu popup 
      Caption         =   "popup"
      Visible         =   0   'False
      Begin VB.Menu mnuDelete 
         Caption         =   "&Delete"
      End
      Begin VB.Menu popStep2 
         Caption         =   "-"
      End
      Begin VB.Menu mnuBringFront 
         Caption         =   "Bring to &Front"
      End
      Begin VB.Menu mnuSendBack 
         Caption         =   "Send to &Back"
      End
      Begin VB.Menu mnuZRef 
         Caption         =   "Change Z-Reference"
      End
      Begin VB.Menu mnuPop1 
         Caption         =   "-"
      End
      Begin VB.Menu mnuProperties 
         Caption         =   "&Properties"
      End
   End
   Begin VB.Menu mnuCaption 
      Caption         =   "Wavesoft Glory Lands Map Editor"
   End
End
Attribute VB_Name = "frmMap"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private Declare Function LockWindowUpdate Lib "user32" (ByVal hwndLock As Long) As Long

Dim WithEvents ImgGrid As clsImgGridObj
Attribute ImgGrid.VB_VarHelpID = -1
Public Designer As clsMapDesigner
Dim Rect As RGN
Dim lRect As RGN
Dim hInst As New clsObjInstance
Dim sBackground As String

' Selection/Design Rectangle
Dim MouseX As Integer, MouseY As Integer    ' Current mouse X,Y in 32x32 Grid coord
Dim LastX As Integer, LastY As Integer      ' The above last coordinates to decrease flickering
Dim BindX As Integer, BindY As Integer      ' The coordinates where the mouse was pressed
Dim MouseUpRect As RGN                      ' The design location of the new object
Dim MouseDown As Boolean

Dim Selecting As Boolean                    ' Turn on/off hover selection
Dim Designing As Boolean                    ' Turn on/off design mode
Dim Repositioning As Boolean                ' Turn on/off hover'ed object repositioning
Dim ZRefChanging As Boolean
Dim AttDrawing As Boolean                   ' Attennuation drawing

Dim AttZOver As Boolean

Dim zRefBase As Integer
Dim hoverID As Long                         ' The ID of the object currently hover'ed

' Temp
Dim hFile As String
Dim hCompFile As String
Dim o As New clsObjIO
Dim Active As Boolean
Dim lMode As enmCompileModes

Public Sub ShiftReset()
    Rect.Left = 0
    Rect.Top = 0
    Designer.SetVisibleRegion Rect.Left, Rect.Top, Rect.Width, Rect.Height
    Designer.Refresh
    RefreshAttMap
    Image1.Move -Rect.Left * 32 - 2, -Rect.Top * 32 - 2
End Sub

Public Sub ShiftRight()
    Rect.Left = Rect.Left + 1
    Designer.SetVisibleRegion Rect.Left, Rect.Top, Rect.Width, Rect.Height
    Designer.Refresh
    RefreshAttMap
    Image1.Move -Rect.Left * 32 - 2, -Rect.Top * 32 - 2
End Sub

Public Sub ShiftLeft()
    Rect.Left = Rect.Left - 1
    If Rect.Left < 0 Then Rect.Left = 0
    Designer.SetVisibleRegion Rect.Left, Rect.Top, Rect.Width, Rect.Height
    Designer.Refresh
    RefreshAttMap
    Image1.Move -Rect.Left * 32 - 2, -Rect.Top * 32 - 2
End Sub

Public Sub ShiftDown()
    Rect.Top = Rect.Top + 1
    Designer.SetVisibleRegion Rect.Left, Rect.Top, Rect.Width, Rect.Height
    Designer.Refresh
    RefreshAttMap
    Image1.Move -Rect.Left * 32 - 2, -Rect.Top * 32 - 2
End Sub

Public Sub ShiftUp()
    Rect.Top = Rect.Top - 1
    If Rect.Top < 0 Then Rect.Top = 0
    Designer.SetVisibleRegion Rect.Left, Rect.Top, Rect.Width, Rect.Height
    Designer.Refresh
    RefreshAttMap
    Image1.Move -Rect.Left * 32 - 2, -Rect.Top * 32 - 2
End Sub

Public Sub Compile(Optional cMode As enmCompileModes = cmPHP)
    On Error GoTo er
    Dim NoDialog As Boolean
    Dim h As New clsCompiledFile
    NoDialog = False
    
    If cMode = smRepeat Then
        ' Default
        If lMode = 0 Then lMode = cmPHP
        
        ' Change mode
        cMode = lMode
        
        ' Do not show dialog if file is already configured
        NoDialog = True
    End If
    
    If cMode = cmPHP Then
        If Not NoDialog Or hCompFile = "" Then
            If hCompFile <> "" Then
                CommonDialog1.FileName = hCompFile
            Else
                If Dir(FixPath(App.Path) & "output", vbDirectory) <> "" Then
                    CommonDialog1.FileName = FixPath(App.Path) & "output\untitled.php"
                End If
            End If
            CommonDialog1.DialogTitle = "Save a compiled file"
            CommonDialog1.Filter = "PHP Script File (*.php) | *.php"
            CommonDialog1.Flags = cdlOFNExplorer Or cdlOFNPathMustExist Or cdlOFNOverwritePrompt
            CommonDialog1.ShowSave
            hCompFile = CommonDialog1.FileName
        End If
            
        ' PHP Compile
        h.BuildMode = cmPHP
        h.ParseDesigner Designer, hCompFile
    
    ElseIf cMode = cmSerialized Then
        If Not NoDialog Or hCompFile = "" Then
            If hCompFile <> "" Then
                CommonDialog1.FileName = hCompFile
            Else
                If Dir(FixPath(App.Path) & "output", vbDirectory) <> "" Then
                    CommonDialog1.FileName = FixPath(App.Path) & "output\untitled.cmp"
                End If
            End If
            CommonDialog1.DialogTitle = "Save a compiled file"
            CommonDialog1.Filter = "Compiled map file (*.cmp) | *.cmp"
            CommonDialog1.Flags = cdlOFNExplorer Or cdlOFNPathMustExist Or cdlOFNOverwritePrompt
            CommonDialog1.ShowSave
            hCompFile = CommonDialog1.FileName
        End If
            
        ' PHP Compile
        h.BuildMode = cmSerialized
        h.ParseDesigner Designer, hCompFile
    
    ElseIf cMode = cmChunk Then
        If Not NoDialog Or hCompFile = "" Then
            If hCompFile <> "" Then
                CommonDialog1.FileName = hCompFile
            Else
                If Dir(FixPath(App.Path) & "output", vbDirectory) <> "" Then
                    CommonDialog1.FileName = FixPath(App.Path) & "output\untitled"
                End If
            End If
            CommonDialog1.DialogTitle = "Save a compiled file"
            CommonDialog1.Filter = "Many Map and Collision files (*.gcmp) | *."
            CommonDialog1.Flags = cdlOFNExplorer Or cdlOFNPathMustExist Or cdlOFNOverwritePrompt
            CommonDialog1.ShowSave
            hCompFile = CommonDialog1.FileName
        End If
            
        ' PHP Compile
        h.BuildMode = cmChunk
        h.ParseDesigner Designer, hCompFile
    
    ElseIf cMode = cmSplit Then
        If Not NoDialog Or hCompFile = "" Then
            If hCompFile <> "" Then
                CommonDialog1.FileName = hCompFile
            Else
                If Dir(FixPath(App.Path) & "output", vbDirectory) <> "" Then
                    CommonDialog1.FileName = FixPath(App.Path) & "output\untitled"
                End If
            End If
            CommonDialog1.DialogTitle = "Save a compiled file"
            CommonDialog1.Filter = "Javascript and ZBuffer chunk files (*.jmap, *.zmap) | *."
            CommonDialog1.Flags = cdlOFNExplorer Or cdlOFNPathMustExist Or cdlOFNOverwritePrompt
            CommonDialog1.ShowSave
            hCompFile = CommonDialog1.FileName
        End If
            
        ' PHP Compile
        h.BuildMode = cmSplit
        h.ParseDesigner Designer, hCompFile
    
    End If
er:
    If cMode <> smRepeat Then lMode = cMode
End Sub

Public Sub SetActiveObject(FileName As String)
    o.LoadFile FixPath(App.Path) & "objects\" & FileName
    Active = True
End Sub

Private Sub Form_DblClick()
    MsgBox Designer.AttGrid.StructPrint
End Sub

Private Sub Form_KeyDown(KeyCode As Integer, Shift As Integer)
    ' [ESC] Abort design
    If KeyCode = 27 Then
        Repositioning = False
        shpSelection.Visible = False
        shpHover.Visible = False
        shpOrigin.Visible = False
        lnZRef.Visible = False
        ZRefChanging = False
        hoverID = 0
        Set Me.MouseIcon = Ico(3).Picture
        
    ' [DEL] Delete hover'ed object
    ElseIf KeyCode = 46 Then
        If hoverID <> 0 Then
            Designer.DeleteObject Designer.Object(hoverID)
            Designer.Refresh
        End If
    
    ' [SHIFT] (On AttDrawing) Change cursor
    ElseIf ((Shift And 1) <> 0) And AttDrawing Then
        Set Me.MouseIcon = Ico(0).Picture
    
    ' [SHIFT] (On designer) Turn on placing mode
    ElseIf ((Shift And 1) <> 0) And Designing And Not MouseDown And Not AttDrawing Then
        shpSelection.Visible = True
        Set Me.MouseIcon = Ico(0).Picture
        
        ' Is placing object static-sized?
        If o.ResizeModel = szFixedSize Then
            shpSelection.Width = o.ObjectWidth * 32
            shpSelection.Height = o.ObjectHeight * 32
            
        ' Else, show a 1x1 cell
        Else
            shpSelection.Width = 32
            shpSelection.Height = 32
        End If
    
    ' [UP]
    ElseIf KeyCode = 38 Then
        ShiftUp
        
    ' [DOWN]
    ElseIf KeyCode = 40 Then
        ShiftDown
        
    ' [LEFT]
    ElseIf KeyCode = 37 Then
        ShiftLeft
        
    ' [RIGHT]
    ElseIf KeyCode = 39 Then
        ShiftRight
        
    ' [SPACE]
    ElseIf KeyCode = 32 Then
        ShiftReset
        
    End If
    Debug.Print KeyCode
End Sub

Private Sub Form_KeyUp(KeyCode As Integer, Shift As Integer)
    If Repositioning Or ZRefChanging Then Exit Sub
    
    ' [SHIFT] (Design) Turn off all actions
    If (KeyCode = 16) And Designing And Not MouseDown And Not AttDrawing Then
        shpSelection.Visible = False
        Set Me.MouseIcon = Ico(3).Picture
        
    ' [SHIFT] (AttDrawing) Change cursor
    ElseIf (KeyCode = 16) And AttDrawing Then
            Set Me.MouseIcon = Ico(3).Picture
        
    ' [CTLR] Turn off hover mode
    ElseIf (KeyCode = 17) Then
        shpHover.Visible = False
        lnZRef.Visible = False
        Set Me.MouseIcon = Ico(3).Picture
        
    End If
    
End Sub

Private Sub Form_MouseDown(Button As Integer, Shift As Integer, X As Single, Y As Single)
    ' Mouse status flag
    MouseDown = True
    
    If ZRefChanging Or AttDrawing Then
    
        ' Do nothing
    
    ElseIf (Button = 1) And Repositioning Then
    
        ' Set Bind positions
        BindX = MouseX
        BindY = MouseY
    
    ElseIf (Button = 1) And Designing Then
        
        ' Make sure we have updates MouseX/Y Coordinates
        Form_MouseMove 0, Shift, X, Y
        
        ' Update the hover selection
        If Selecting Then HighLightHover ((Shift And 2) <> 0)
        
        ' If we are above another object, require SHIFT pressed
        ' in order to append it
        If (hoverID <> 0) And ((Shift And 1) = 0) Then Exit Sub
        
        ' Flicker-free display
        LockWindowUpdate hWnd
        
        ' Store Bind Point
        BindX = MouseX
        BindY = MouseY
        shpSelection.Left = MouseX * 32
        shpSelection.Top = MouseY * 32
        
        ' Initialize Selection
        shpSelection.Width = 32
        shpSelection.Height = 32
        shpSelection.Visible = True
        
        ' Change color to RED if it is somehow locked
        If o.ResizeModel = szFixedSize Then
            shpSelection.BorderColor = &HFF&
            shpSelection.Width = o.ObjectWidth * 32
            shpSelection.Height = o.ObjectHeight * 32
        Else
            shpSelection.BorderColor = &HFF0000
        End If
            
        ' Flicker-free display
        LockWindowUpdate 0
        
        ' Perform the initialization
        LastX = -1                             ' Override flickering check
        Form_MouseMove Button, Shift, X, Y
    
    End If
End Sub

Private Sub HoverObject(ByVal Id As Integer)
    Dim lP, tP, W, h
    W = Designer.Object(Id).Width * 32
    h = Designer.Object(Id).Height * 32
    lP = (Designer.Object(Id).Left - Rect.Left) * 32
    tP = (Designer.Object(Id).Top - Rect.Top) * 32
    shpHover.Move lP, tP, W, h
    shpHover.Visible = True
End Sub

Private Sub HighLightHover(Optional ShowShape As Boolean = True, Optional ByVal OtherX As Integer, Optional ByVal OtherY As Integer)
    Dim i, lP, tP, W, h
    Dim iX As Integer, iY As Integer
    
    iX = MouseX
    iY = MouseY
    If IsMissing(OtherX) Then iX = OtherX
    If IsMissing(OtherY) Then iY = OtherY

    i = Designer.ObjectIdFromPoint(iX + Rect.Left, iY + Rect.Top)
    If (i <> 0) Then
        If (hoverID <> i) Then
            W = Designer.Object(i).Width * 32
            h = Designer.Object(i).Height * 32
            lP = (Designer.Object(i).Left - Rect.Left) * 32
            tP = (Designer.Object(i).Top - Rect.Top) * 32
            shpHover.Move lP, tP, W, h
            shpHover.Visible = ShowShape
            lnZRef.Y1 = tP + h + (Designer.Object(i).ZOffset * 32)
            lnZRef.Y2 = lnZRef.Y1
            lnZRef.Visible = ShowShape
            If ShowShape Then
                Set Me.MouseIcon = Ico(4).Picture
            End If
        End If
    Else
        shpHover.Visible = False
        lnZRef.Visible = False
        If ShowShape Then
            Set Me.MouseIcon = Ico(5).Picture
        End If
    End If
    hoverID = i
End Sub

Private Sub StartRepositionMode()
    ' Make sure we have a selected object
    If hoverID = 0 Then Exit Sub
    
    ' Prepare UI
    shpSelection.BorderColor = &HC000&
    shpSelection.Width = Designer.Object(hoverID).Width * 32
    shpSelection.Height = Designer.Object(hoverID).Height * 32
    shpSelection.Left = MouseX * 32
    shpSelection.Top = MouseY * 32
    shpSelection.Visible = True
    shpOrigin.Move Designer.Object(hoverID).Left * 32, Designer.Object(hoverID).Top * 32, shpSelection.Width, shpSelection.Height
    shpOrigin.Visible = True
    Set Me.MouseIcon = Ico(1).Picture
    
    ' Set the proper flags
    Repositioning = True
    
    ' Init the rect
    MouseUpRect.Left = MouseX + Rect.Left
    MouseUpRect.Top = MouseY + Rect.Top
    MouseUpRect.Width = Designer.Object(hoverID).Width
    MouseUpRect.Height = Designer.Object(hoverID).Height
End Sub

Private Sub Form_MouseMove(Button As Integer, Shift As Integer, X As Single, Y As Single)
    frmMain.UnSelList
    MouseX = X \ 32
    MouseY = Y \ 32
    
    ' Decrease flickering: Feed events only when location changed in 32x32 coord system
    If (LastX <> MouseX) Or (LastY <> MouseY) Then
    
        If AttDrawing Then
        
            ' Move the shape over the mouse position
            shpSelection.Move MouseX * 32, MouseY * 32, 32, 32
            
            ' Set Attennuation if shift is pressed
            If Shift And 1 Then
                SetAtt MouseX, MouseY, 100 - frmMain.AttValue
            End If
    
        ElseIf (Button = 0) And ZRefChanging Then
        
            ' Move Z-Ref Line
            lnZRef.Y1 = (MouseY + 1) * 32
            lnZRef.Y2 = lnZRef.Y1
        
        ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        ' !! Reposition Checks before !!
        ' !!      design checks       !!
        ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        
        ' Not pressed and in repositioning mode?
        ElseIf (Button = 0) And Repositioning Then
            
            ' Flicker-free display
            LockWindowUpdate hWnd

            ' Move the shape over the mouse position
            shpSelection.Left = MouseX * 32
            shpSelection.Top = MouseY * 32
        
            ' Update selection info
            MouseUpRect.Left = MouseX + Rect.Left
            MouseUpRect.Top = MouseY + Rect.Top
        
            ' Flicker-free display
            LockWindowUpdate 0
        
        ' Not pressed? Hover check..
        ElseIf (Button = 0) Then
        
            ' Trigger the event only when needed
            If Selecting Then HighLightHover ((Shift And 2) <> 0)
        
            ' If shift is pressed (Insert Object Mode) display rectangle also
            If shpSelection.Visible Then
                shpSelection.Move MouseX * 32, MouseY * 32
            End If
        
        ' Pressed and designing or repositioning? Display selection rectangle
        ElseIf (Button = 1) And (Designing Or Repositioning) Then
            Dim xP, yP, xW, yH, Sized As Boolean
            
            Sized = False
            
            ' Flicker-free display
            LockWindowUpdate hWnd
            
            ' In case of fixed-sized model, keep size the same
            
            ' Case1 : HOVER Mode
            If (hoverID <> 0) And Repositioning Then
                If Designer.Object(hoverID).PenMode = bltNoStretch Then
                    xP = MouseX
                    yP = MouseY
                    
                    '  In case of reposition, keep original object's W/H
                    xW = Designer.Object(hoverID).Width
                    yH = Designer.Object(hoverID).Height
                    
                    ' Yes, we resized it
                    Sized = True
                End If
            
            ' Case2 : DESIGN Mode
            ElseIf (o.ResizeModel = szFixedSize) Then
            
                xP = MouseX
                yP = MouseY
                
                '  In case of reposition, keep original object's W/H
                xW = o.ObjectWidth
                yH = o.ObjectHeight
                
                ' Yes, we resized it
                Sized = True
            End If
                
            ' Else, strech based on selection
            If Not Sized Then
                xP = BindX
                yP = BindY
                xW = MouseX - BindX + 1
                yH = MouseY - BindY + 1
                If xW < 0 Then
                    xP = xP + xW
                    xW = -xW
                End If
                If yH < 0 Then
                    yP = yP + yH
                    yH = -yH
                End If
            End If
            
            
            ' Move the rectange
            shpSelection.Move xP * 32, yP * 32, xW * 32, yH * 32
            
            ' And save info in RGN
            MouseUpRect.Left = xP + Rect.Left
            MouseUpRect.Top = yP + Rect.Top
            MouseUpRect.Width = xW
            MouseUpRect.Height = yH
            
            
            ' Flicker-free display
            LockWindowUpdate 0
        End If
    End If
    LastX = MouseX
    LastY = MouseY
End Sub

Private Sub SetAtt(ByVal X As Integer, ByVal Y As Integer, ByVal Value As Integer)
    ' Calculate att element on x/y pos
    Dim atID As Integer
    atID = GetAttFromPoint(X, Y)
    lblAtt(atID).Tag = Value
    ProprietizeAtt atID
    Designer.AttGrid(MouseX + Rect.Left)(MouseY + Rect.Top) = Value
End Sub

Private Sub Form_MouseUp(Button As Integer, Shift As Integer, X As Single, Y As Single)
    MouseDown = False
    
    If AttDrawing Then
        
        If Button = 2 Then
            ' On right-click, swap the states between solid (100%) and transparent (0%)
            ' Default swaps to 100%
            If Designer.AttGrid(MouseX + Rect.Left)(MouseY + Rect.Top) = 100 Then
                SetAtt MouseX, MouseY, 0
            Else
                SetAtt MouseX, MouseY, 100
            End If
        Else
            ' Set Attennuation
            SetAtt MouseX, MouseY, 100 - frmMain.AttValue
        End If
        
    ' Z-Reference line position changing
    ElseIf ZRefChanging Then
    
        ' Turn of changing
        lnZRef.Visible = False
        ZRefChanging = False
        shpHover.Visible = True
        
        ' Save Z-Reference
        Dim zmY As Integer
        zmY = MouseY + Rect.Top
        Designer.Object(hoverID).ZOffset = zmY - Designer.Object(hoverID).Bottom
        
        ' Restore UI
        Set Me.MouseIcon = Ico(3).Picture
    
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    ' !! Reposition Checks before !!
    ' !!      design checks       !!
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    
    ' Only when repositioning (shpSelection visibility check is to abort script when ESC is hit)
    ElseIf (Button = 1) And shpSelection.Visible And Repositioning Then
        shpSelection.Visible = False
        shpOrigin.Visible = False
        
        If hoverID <> 0 Then
            Designer.Object(hoverID).Place MouseUpRect.Left, MouseUpRect.Top, MouseUpRect.Width, MouseUpRect.Height
            Designer.Refresh
            Repositioning = False
        End If
        
        ' Object location changed. Clean Hover
        shpHover.Visible = False
        lnZRef.Visible = False
        Set Me.MouseIcon = Ico(3).Picture
    
    ' Only when designing (shpSelection visibility check is to abort script when ESC is hit)
    ElseIf (Button = 1) And shpSelection.Visible And Designing Then
        ' Exclude missing or invalid object files
        If Not o.Loaded Then Exit Sub
        
        shpSelection.Visible = False
        
        Set hInst = o.CreateInstance()
        hInst.Place MouseUpRect.Left, MouseUpRect.Top, MouseUpRect.Width, MouseUpRect.Height
        Designer.PutObject hInst
        Designer.Refresh
        
        ' Object location changed. Clean Hover
        shpHover.Visible = False
        lnZRef.Visible = False
        
    ' Only when we click a hover'ed object
    ElseIf (Button = 1) And (hoverID <> 0) Then
        
        ' Reposition the object
        StartRepositionMode
        
        ' Init events
        LastX = -1
        Form_MouseMove 0, Shift, X, Y
            
    ' Right-click? Drop menu
    ElseIf (Button = 2) Then
        If hoverID <> 0 Then
            PopupMenu popup, vbPopupMenuLeftAlign, X, Y
        End If
    End If
    
End Sub

Private Sub Img_MouseDown(Index As Integer, Button As Integer, Shift As Integer, X As Single, Y As Single)
    Form_MouseDown Button, Shift, (X / Screen.TwipsPerPixelX) + Img(Index).Left, (Y / Screen.TwipsPerPixelY) + Img(Index).Top
End Sub

Private Sub Img_MouseMove(Index As Integer, Button As Integer, Shift As Integer, X As Single, Y As Single)
    Form_MouseMove Button, Shift, (X / Screen.TwipsPerPixelX) + Img(Index).Left, (Y / Screen.TwipsPerPixelY) + Img(Index).Top
End Sub

Private Sub Img_MouseUp(Index As Integer, Button As Integer, Shift As Integer, X As Single, Y As Single)
    Form_MouseUp Button, Shift, (X / Screen.TwipsPerPixelX) + Img(Index).Left, (Y / Screen.TwipsPerPixelY) + Img(Index).Top
End Sub

' ----------------------------- Image Grid Events --------------------------------------
Private Sub ImgGrid_AllocateImage(X As Integer, Y As Integer, Image As stdole.StdPicture, IndexID As Integer)
    Dim i
    i = Img.UBound + 1
    Load Img(i)
    Img(i).Left = X
    Img(i).Top = Y
    Set Img(i).Picture = Image
    Img(i).Visible = True
    IndexID = i
End Sub
Private Sub ImgGrid_AlterImage(Index As Integer, Image As stdole.StdPicture)
    Set Img(Index).Picture = Image
End Sub
Private Sub ImgGrid_DestroyImage(Index As Integer)
    On Error Resume Next
    Img(Index).Visible = False
    Unload Img(Index)
End Sub
Private Sub ImgGrid_ZOrderBack(Image As Integer, ByVal X As Integer, ByVal Y As Integer)
    Img(Image).ZOrder 1
    On Error GoTo er
    lblAtt(GetAttFromPoint(X, Y)).ZOrder IIf(AttZOver, 0, 1)
er:
End Sub
' ----------------------------- Image Grid Events --------------------------------------

Private Sub Form_Load()
    Active = False
    
    ' Initialize grid and designer
    Set ImgGrid = New clsImgGridObj
    Set Designer = New clsMapDesigner
    
    ' Bind Designer into the grid
    Designer.Init ImgGrid, Me.hWnd
    
    ' Initial rect (Width is set by Form_Resize)
    Rect.Left = 0
    Rect.Top = 0
    
    ' Initialize interface
    shpSelection.Width = 32
    shpSelection.Height = 32
    Selecting = True
    Designing = True
    
    AttDrawing = False
End Sub

Private Sub Form_Resize()
    On Error Resume Next
    Rect.Width = Int(Me.Width / (32 * Screen.TwipsPerPixelX))
    Rect.Height = Int((Me.Height - 320) / (32 * Screen.TwipsPerPixelY))
    If (Rect.Width <> lRect.Width) Or (Rect.Height <> lRect.Height) Then
        Debug.Print Rect.Width; "x"; Rect.Height
        Designer.SetVisibleRegion Rect.Left, Rect.Top, Rect.Width, Rect.Height
        Designer.Refresh
        RebuildAttMap
    End If
    lRect = Rect
    SetBackground Designer.BackgroundImage
    lnZRef.X1 = 0
    lnZRef.X2 = Me.Width
End Sub

Private Sub lblAtt_MouseDown(Index As Integer, Button As Integer, Shift As Integer, X As Single, Y As Single)
    Form_MouseDown Button, Shift, (X / Screen.TwipsPerPixelX) + lblAtt(Index).Left, Y / Screen.TwipsPerPixelY + lblAtt(Index).Top
End Sub

Private Sub lblAtt_MouseMove(Index As Integer, Button As Integer, Shift As Integer, X As Single, Y As Single)
    Form_MouseMove Button, Shift, (X / Screen.TwipsPerPixelX) + lblAtt(Index).Left, Y / Screen.TwipsPerPixelY + lblAtt(Index).Top
End Sub

Private Sub lblAtt_MouseUp(Index As Integer, Button As Integer, Shift As Integer, X As Single, Y As Single)
    Form_MouseUp Button, Shift, (X / Screen.TwipsPerPixelX) + lblAtt(Index).Left, Y / Screen.TwipsPerPixelY + lblAtt(Index).Top
End Sub

Private Sub mnuBringFront_Click()
    If hoverID = 0 Then Exit Sub
    
    ' Send hover'ed object to back
    Designer.BringToFront hoverID
    Designer.Refresh
End Sub

Private Sub mnuDelete_Click()
    If hoverID <> 0 Then
        Designer.DeleteObject Designer.Object(hoverID)
        Designer.Refresh
    End If
End Sub

Private Sub mnuSendBack_Click()
    If hoverID = 0 Then Exit Sub
    
    ' Send hover'ed object to back
    Designer.SendToBack hoverID
    Designer.Refresh
End Sub

Public Sub SetBackground(ByVal TileName As String)
    Dim hPic As StdPicture
    Designer.BackgroundImage = TileName
    Set hPic = ImgLib.Image(TileName)
    If hPic Is Nothing Then Exit Sub
    Dim X, Y
    Me.Cls
    For X = 0 To Me.Width / Screen.TwipsPerPixelX Step 32
        For Y = 0 To Me.Height / Screen.TwipsPerPixelY Step 32
            Me.PaintPicture hPic, X, Y
        Next Y
    Next X
End Sub

Public Sub Save()
    If hFile = "" Then
        SaveAs
    Else
        Designer.SaveDesigner hFile
    End If
End Sub

Public Sub SaveAs()
    On Error GoTo er
    If hFile <> "" Then
        CommonDialog1.FileName = hFile
    Else
        If Dir(FixPath(App.Path) & "maps", vbDirectory) <> "" Then
            CommonDialog1.FileName = FixPath(App.Path) & "maps\untitled.gmap"
        End If
    End If
    CommonDialog1.DialogTitle = "Save a MAP file"
    CommonDialog1.Filter = "GloryLands Map File (*.gmap) | *.gmap"
    CommonDialog1.Flags = cdlOFNExplorer Or cdlOFNPathMustExist Or cdlOFNOverwritePrompt
    CommonDialog1.ShowSave
    hFile = CommonDialog1.FileName
    Designer.SaveDesigner CommonDialog1.FileName
er:
End Sub

Public Sub LoadFile()
    On Error GoTo er
    If hFile <> "" Then
        CommonDialog1.FileName = hFile
    Else
        If Dir(FixPath(App.Path) & "maps", vbDirectory) <> "" Then
            CommonDialog1.FileName = FixPath(App.Path) & "maps\*.gmap"
        End If
    End If
    CommonDialog1.DialogTitle = "Open a MAP file"
    CommonDialog1.Filter = "GloryLands Map File (*.gmap) | *.gmap"
    CommonDialog1.Flags = cdlOFNExplorer Or cdlOFNPathMustExist Or cdlOFNFileMustExist
    CommonDialog1.ShowOpen
    hFile = CommonDialog1.FileName
    Designer.LoadDesigner CommonDialog1.FileName
    Designer.Refresh
    SetBackground Designer.BackgroundImage
    RebuildAttMap
er:
End Sub

Private Sub mnuZRef_Click()
    If hoverID = 0 Then Exit Sub
    lnZRef.Visible = True
    ZRefChanging = True
    HoverObject hoverID
    Set Me.MouseIcon = Ico(2).Picture
End Sub

Private Function GetAttFromPoint(ByVal X As Integer, ByVal Y As Integer) As Integer
    GetAttFromPoint = Rect.Width * Y + X
End Function

Private Sub RefreshAttMap()
    Dim i, X, Y
    i = 0
    LockWindowUpdate hWnd
    For Y = 0 To Rect.Height - 1
        For X = 0 To Rect.Width - 1
            lblAtt(i).Tag = Designer.AttGrid(X + Rect.Left)(Y + Rect.Top)
            ProprietizeAtt i
            i = i + 1
        Next X
    Next Y
    LockWindowUpdate 0
End Sub

Private Sub RebuildAttMap()
    Dim i, X, Y
    ' Rearrange items (keep created ones)
    LockWindowUpdate hWnd
    For Y = 0 To Rect.Height - 1
        For X = 0 To Rect.Width - 1
            If i = lblAtt.Count Then Load lblAtt(i)
            lblAtt(i).Visible = AttDrawing
            lblAtt(i).Move X * 32, Y * 32, 32, 32
            lblAtt(i).ZOrder IIf(AttZOver, 0, 1)
            lblAtt(i).Tag = Designer.AttGrid(X + Rect.Left)(Y + Rect.Top)
            ProprietizeAtt i
            i = i + 1
        Next X
    Next Y
    LockWindowUpdate 0
    
    ' Unload remaining items
    If i < lblAtt.UBound Then
        Dim j
        For j = i To lblAtt.UBound
            Unload lblAtt(j)
        Next j
    End If
End Sub

Public Sub SetAttDrawing(Value As Boolean)
    Dim i
    If AttDrawing <> Value Then
        ' Send "ESC" to cancel any active operation
        Form_KeyDown 27, 0
                
        ' Set attributes
        AttDrawing = Value
        shpSelection.Visible = Value
        For i = 0 To lblAtt.UBound
            lblAtt(i).Visible = AttDrawing
        Next i
    End If
End Sub

Public Sub SetAttOvergrid(Value As Boolean)
    If Value <> AttZOver Then
        AttZOver = Value
        RebuildAttMap
    End If
End Sub

Private Sub ProprietizeAtt(ByVal i As Integer)
    Dim V
    V = lblAtt(i).Tag
    If V = 100 Then
        lblAtt(i).BackStyle = 1
        lblAtt(i).Caption = ""
        lblAtt(i).BackColor = &H0
    ElseIf V = 0 Then
        lblAtt(i).BackStyle = 0
        lblAtt(i).Caption = ""
    Else
        lblAtt(i).BackStyle = 0
        lblAtt(i).Caption = V
    End If
End Sub
