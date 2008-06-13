VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "clsObjInstance"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit
Private Declare Sub CopyMemory Lib "kernel32" Alias "RtlMoveMemory" (Destination As Any, Source As Any, ByVal Length As Long)

' Save chunk format
Private Type SaveHead
    Left As Integer
    Top As Integer
    Width As Integer
    Height As Integer
    RandomizeCenter As Boolean
    PureSize As COF_Head_PureSize
    PenMode As Byte
End Type

Private Type pixelGrid
    X As Integer
    Y As Integer
    Z As Integer
    A As Integer
    Layers As Byte
    Layer(3) As String
End Type

' External
Dim RandomizeCenter As Boolean
Dim PureSize As COF_Head_PureSize
Dim Chunks() As RAM_Chunk
Public PenMode As BlitMode
Public InstanceOf As String
' ----------

' Internal
Dim hObjectName As String
Public Left As Integer
Public Top As Integer
Public BaseLayer As Integer
Public Width As Integer
Public Height As Integer
Public Right As Integer
Public Bottom As Integer
Public ZOffset As Integer

Public GlobalOffsetX As Integer
Public GlobalOffsetY As Integer

Dim myDesigner As clsMapDesigner

' A cache for easier compiling
Dim pgWidth As Integer
Dim pgHeight As Integer
Dim pgOfsLeft As Integer
Dim pgOfsTop As Integer
Dim pgGrid() As pixelGrid
Dim pgExists As Boolean

Dim VisibleRGN As RGN

Private Sub FeedBlitEvent(ByVal X As Integer, ByVal Y As Integer, ByVal Layer As Integer, ByVal Image As String)
    'myDesigner.ImageGrid.SetImg x - GlobalOffsetX, y - GlobalOffsetY, Layer, "", ImgLib.Image(Image)
    myDesigner.ImageGrid.PenAction X - GlobalOffsetX, Y - GlobalOffsetY, Layer, "", ImgLib.Image(Image), pmInsertAutoLayer
End Sub

Public Sub BindTo(Designer As clsMapDesigner)
    Set myDesigner = Designer
End Sub

Public Sub Place(ByVal X As Integer, ByVal Y As Integer, ByVal Width As Integer, ByVal Height As Integer)
    ' Modify the object's coordinates
    Me.Left = X
    Me.Top = Y
    Me.Width = Width
    Me.Height = Height
    Me.Right = Me.Left + Me.Width - 1
    Me.Bottom = Me.Top + Me.Height - 1
End Sub

Public Sub BlitTo(Optional ByVal RegionLeft As Integer = 0, Optional ByVal RegionTop As Integer = 0, Optional ByVal RegionWidth As Integer = 0, Optional ByVal RegionHeight As Integer = 0, Optional OffsetX As Integer, Optional OffsetY As Integer)
    ' Calculate the visible region
    If (RegionWidth >= Width) Then
        VisibleRGN.Width = Width
    Else
        VisibleRGN.Width = RegionWidth
    End If
    If (RegionHeight >= Height) Then
        VisibleRGN.Height = Height
    Else
        VisibleRGN.Height = RegionHeight
    End If
    VisibleRGN.Left = RegionLeft
    VisibleRGN.Top = RegionTop
    
    ' Exclude invalid regions
    If (VisibleRGN.Left < 0) Or (VisibleRGN.Top < 0) Then Exit Sub
    If (VisibleRGN.Width <= 0) Or (VisibleRGN.Height <= 0) Then Exit Sub
    
    ' Set Global offsets
    GlobalOffsetX = OffsetX
    GlobalOffsetY = OffsetY
    
    ' Design the object
    BlitGrid Left, Top, Width, Height, Chunks, PenMode
End Sub

Private Sub RangeBlit(ByVal PosX As Integer, ByVal PosY As Integer, ByVal RangeW As Integer, ByVal RangeH As Integer, Grid As RAM_Chunk, Optional Randomized As Boolean = False)
    Dim X, Y, l, oX, oY
    oX = 0
    oY = 0
    
    
    For l = 0 To Grid.Layers - 1
        For Y = PosY To PosY + RangeH - 1
            If Not Randomized Then oX = 0
            For X = PosX To PosX + RangeW - 1
                
                ' If randomized data, create positions
                If Randomized Then
                    oX = Fix(Rnd * Grid.Width)
                    oY = Fix(Rnd * Grid.Height)
                End If
                
                ' Store compile grid info
                If l = 0 Then
                    ' (Only once)
                    pgGrid(X - pgOfsLeft, Y - pgOfsTop).X = X 'Grid.MapInfo(oX, oY).MapX
                    pgGrid(X - pgOfsLeft, Y - pgOfsTop).Y = Y ' Grid.MapInfo(oX, oY).MapZ 'Grid.MapInfo(oX, oY).MapY
                    pgGrid(X - pgOfsLeft, Y - pgOfsTop).Z = Grid.MapInfo(oX, oY).MapZ
                    pgGrid(X - pgOfsLeft, Y - pgOfsTop).A = Grid.MapInfo(oX, oY).Attennuation
                End If
                pgGrid(X - pgOfsLeft, Y - pgOfsTop).Layer(l) = Grid.FileNames(oX, oY, l)
                pgGrid(X - pgOfsLeft, Y - pgOfsTop).Layers = Grid.Layers
                
                ' Feed blit event on visible region
                If (X - Left >= VisibleRGN.Left) And (X - Left < VisibleRGN.Left + VisibleRGN.Width) And _
                   (Y - Top >= VisibleRGN.Top) And (Y - Top < VisibleRGN.Top + VisibleRGN.Height) Then
                    
                    FeedBlitEvent X, Y, l, Grid.FileNames(oX, oY, l) ', Grid.LocationInfo(l, oX, oY).Elevation, Grid.LocationInfo(l, oX, oY).Attennuation
                
                End If
                
                ' If not randomized data, process with a hidden internal 'for' structure
                If Not Randomized Then
                    oX = oX + 1
                    If (oX >= Grid.Width) Then oX = 0
                End If
            Next X
            
            ' If not randomized data, process with a hidden internal for
            If Not Randomized Then
                oY = oY + 1
                If (oY >= Grid.Height) Then oY = 0
            End If
        Next Y
    Next l
End Sub

Private Sub BlitGrid(ByVal PosX As Integer, ByVal PosY As Integer, ByVal RangeW As Integer, ByVal RangeH As Integer, ObjData() As RAM_Chunk, Optional Mode As BlitMode = bltCenterStretch)
        
    Dim i
    Dim MaxTopH As Integer
    Dim MaxBotH As Integer
    Dim MaxMidH As Integer
    Dim MaxLeftW As Integer
    Dim MaxRightW As Integer
    Dim MaxMidW As Integer
    
    ' 1) Retrive the maximum height/widths
    MaxTopH = PureSize.TH
    MaxBotH = PureSize.BH
    MaxMidH = PureSize.MH
    MaxLeftW = PureSize.LW
    MaxRightW = PureSize.rW
    MaxMidW = PureSize.MW
    
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
    
    ' Prepare the compile cache array
    pgWidth = RangeW
    pgHeight = RangeH
    pgOfsLeft = PosX
    pgOfsTop = PosY
    ReDim pgGrid(MaxLeftW + MaxMidW + MaxRightW - 1, MaxTopH + MaxMidH + MaxBotH - 1)
    
    ' 3) Blit the grids
    RangeBlit PosX + 0, PosY + 0, MaxLeftW, MaxTopH, ObjData(0)
    RangeBlit PosX + MaxLeftW, PosY + 0, MaxMidW, MaxTopH, ObjData(1)
    RangeBlit PosX + MaxLeftW + MaxMidW, PosY + 0, MaxRightW, MaxTopH, ObjData(2)
    
    RangeBlit PosX + 0, PosY + MaxTopH, MaxLeftW, MaxMidH, ObjData(3)
    RangeBlit PosX + MaxLeftW, PosY + MaxTopH, MaxMidW, MaxMidH, ObjData(4), RandomizeCenter
    RangeBlit PosX + MaxLeftW + MaxMidW, PosY + MaxTopH, MaxRightW, MaxMidH, ObjData(5)
    
    RangeBlit PosX + 0, PosY + MaxTopH + MaxMidH, MaxLeftW, MaxBotH, ObjData(6)
    RangeBlit PosX + MaxLeftW, PosY + MaxTopH + MaxMidH, MaxMidW, MaxBotH, ObjData(7)
    RangeBlit PosX + MaxLeftW + MaxMidW, PosY + MaxTopH + MaxMidH, MaxRightW, MaxBotH, ObjData(8)

    ' Now we also have a compile cache
    pgExists = True
End Sub

Private Sub Class_Initialize()
    PureSize = cPS
    Chunks = cChunks
    RandomizeCenter = cRandomizedCenter
    hObjectName = cObjectName
    
    If cBlitMode = szBoxStretch Then
        If cBlitParam = szBothSideStretch Then
            PenMode = bltBothSideStretch
        ElseIf cBlitParam = szCenterStretch Then
            PenMode = bltCenterStretch
        ElseIf cBlitParam = szHSideStretch Then
            PenMode = bltHSideStretch
        ElseIf cBlitParam = szVSideStretch Then
            PenMode = bltVSideStretch
        End If
    ElseIf cBlitMode = szFixedSize Then
        PenMode = bltNoStretch
    ElseIf cBlitMode = szNormalStretch Then
        ' Not implemented
    End If
    
    Left = 0
    Top = 0
    Width = 1
    Height = 1
    Right = 1
    Bottom = 1
    
    ' Notify the system we have no compile cache
    pgExists = False
    
End Sub

Private Sub LPush(Buf As String, ByVal l As Long)
    Dim S As String
    S = Space$(4)
    CopyMemory ByVal S, l, 4
    Buf = Buf & S
End Sub

Private Sub IPush(Buf As String, ByVal l As Integer)
    Dim S As String
    S = Space$(2)
    CopyMemory ByVal S, l, 2
    Buf = Buf & S
End Sub

Private Function LPop(Buf As String) As Long
    Dim S As String
    If Len(Buf) < 4 Then Exit Function
    S = Mid(Buf, Len(Buf) - 3)
    Buf = Mid(Buf, 1, Len(Buf) - 4)
    
    CopyMemory LPop, ByVal S, 4
End Function

Private Function IPop(Buf As String) As Integer
    Dim S As String
    If Len(Buf) < 2 Then Exit Function
    S = Mid(Buf, Len(Buf) - 1)
    Buf = Mid(Buf, 1, Len(Buf) - 2)
    
    CopyMemory IPop, ByVal S, 2
End Function

Private Sub SPush(Buf As String, ByVal S As String)
    Buf = Buf & S
    IPush Buf, Len(S)
End Sub

Private Function SPop(Buf As String) As String
    Dim i
    i = IPop(Buf)
    SPop = Mid(Buf, Len(Buf) - i + 1)
    Buf = Mid(Buf, 1, Len(Buf) - i)
End Function

Public Sub SaveChunk(ByVal File As Long)
    Dim B As Byte
    
    ' Instance name (Read by the designer while loading)
    B = Len(InstanceOf)
    Put #File, , B
    Put #File, , InstanceOf
    
    ' Position Variables
    Put #File, , Left
    Put #File, , Top
    Put #File, , Width
    Put #File, , Height
    Put #File, , Right
    Put #File, , Bottom
    Put #File, , ZOffset
    
End Sub

Public Sub BuildCompileChunk(HostFile As clsCompiledFile)
    ' If we have no compile cache grid, create it now by forcing the re-design of the tile (even if it is not visible)
    If Not pgExists Then BlitTo Me.Left, Me.Top, Me.Width, Me.Height, 0, 0
    
    ' For each grid item blit it on Compile file
    Dim X, Y
    For X = 0 To pgWidth - 1
        For Y = 0 To pgHeight - 1
            HostFile.PutTileOnGrid pgGrid(X, Y).X, pgGrid(X, Y).Y, pgGrid(X, Y).Z + ZOffset, pgGrid(X, Y).Layer
        Next Y
    Next X
End Sub

Public Sub BuildFromSaveChunk(ByVal File As Long)

    Get #File, , Left
    Get #File, , Top
    Get #File, , Width
    Get #File, , Height
    Get #File, , Right
    Get #File, , Bottom
    Get #File, , ZOffset
    
End Sub

