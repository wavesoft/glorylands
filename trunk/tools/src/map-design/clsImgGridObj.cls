VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "clsImgGridObj"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

Private Type Cell
    ' Tile Design Information
    ImageIndexes() As Integer
    ImageFilenames() As String
    Used() As Boolean
    Initialized As Boolean
    
    ' ZBuffering information
    Z() As Integer
    Height() As Integer
    
    ' Grid movement information
    Attenuation As Integer
    Elevation As Integer
End Type

Private Const MAX_LAYERS = 5
Private Const OPTIMIZE_KEEPOBJECTS = False

Public BackgroundFilename As String

Dim Grid() As Cell
Dim GridW As Integer
Dim GridH As Integer

' Called when the host object must create an image instance (MUST! Return an index)
Public Event AllocateImage(X As Integer, Y As Integer, Image As StdPicture, ByRef IndexID As Integer)

' Called when an image must be z-ordered to back
Public Event ZOrderBack(Image As Integer, ByVal X As Integer, ByVal Y As Integer)

' Called when the content of a image must be altered
Public Event AlterImage(Index As Integer, Image As StdPicture)

' Called when an image object is not used any longer and thus can be removed
Public Event DestroyImage(Index As Integer)

' Get a free layer of tile
Private Function FreeLayerOf(ByVal X As Integer, ByVal Y As Integer) As Integer
    Dim i
    
    ' Out of grid? Return layer #0
    If (X >= GridW) Or (Y >= GridH) Then
        FreeLayerOf = 0
        Exit Function
    End If
    
    ' Not initialize? Initialize on the layer #0
    If Not Grid(X, Y).Initialized Then
        FreeLayerOf = 0
        Exit Function
    End If
    
    ' Find an unused layer starting from layer #0
    For i = 0 To 5
        If Not Grid(X, Y).Used(i) Then
            FreeLayerOf = i
            Exit Function
        End If
    Next i
    
    ' Not found? Return the first layer
    FreeLayerOf = 0
End Function

' Perform a pen actino
Public Sub PenAction(ByVal X As Integer, ByVal Y As Integer, ByVal Layer As Byte, ByVal FileName As String, Optional Image As StdPicture = Nothing, Optional cPenMode As PenMode = pmInsert)
    If cPenMode = pmInsert Then
        SetImg X, Y, Layer, FileName, Image
    ElseIf cPenMode = pmInsertAutoLayer Then
        Layer = FreeLayerOf(X, Y)
        SetImg X, Y, Layer, FileName, Image
    ElseIf cPenMode = pmErase Then
        DelImg X, Y, Layer
    ElseIf cPenMode = pmShift_1Up Then
        ZShift X, Y, 1
    ElseIf cPenMode = pmShift_5Up Then
        ZShift X, Y, 5
    ElseIf cPenMode = pmShift_1Down Then
        ZShift X, Y, -1
    ElseIf cPenMode = pmShift_5Down Then
        ZShift X, Y, -5
    End If
End Sub

' Erase an image from somewere
Public Sub DelImg(ByVal X As Integer, ByVal Y As Integer, ByVal Layer As Byte)
    ' If out of range .. don't bother
    If (X >= GridW) Or (Y >= GridH) Then Exit Sub
            
    ' Not even initialized? .. don't bother
    If Not Grid(X, Y).Initialized Then Exit Sub
    
    ' Layer isn't used? .. don't bother
    If Not Grid(X, Y).Used(Layer) Then Exit Sub
    
    ' What delete policy to use?
    If OPTIMIZE_KEEPOBJECTS Then
    
        ' [Optimized for drawing speed]
        ' This policy keeps the image object and deletes only the content
        
        ' Change image to nothing
        RaiseEvent AlterImage(Grid(X, Y).ImageIndexes(Layer), Nothing)
        
        ' (The rest of the usage and layer information are being cept)
    
    Else
    
        ' [Optimized for memory]
        ' This policy deletes both the content and the image
        
        ' Delete image object
        RaiseEvent DestroyImage(Grid(X, Y).ImageIndexes(Layer))
        
        ' Cleanup layer information
        Grid(X, Y).Used(Layer) = False
        Grid(X, Y).ImageFilenames(Layer) = ""
        Grid(X, Y).ImageIndexes(Layer) = 0
    End If
End Sub

' Initialize the cell if not initialized
Private Sub InitCell(ByVal X As Integer, ByVal Y As Integer)
    If Not Grid(X, Y).Initialized Then
        ReDim Grid(X, Y).ImageIndexes(5)
        ReDim Grid(X, Y).ImageFilenames(5)
        ReDim Grid(X, Y).Used(5)
        ReDim Grid(X, Y).Z(5)
        ReDim Grid(X, Y).Height(5)
        Grid(X, Y).Initialized = True
    End If
End Sub

' Put an image somewere
Public Sub SetImg(ByVal X As Integer, ByVal Y As Integer, ByVal Layer As Byte, ByVal FileName As String, Optional Image As StdPicture = Nothing)
    Dim nW As Integer, nH As Integer
    On Error Resume Next
    
    ' Calculate and resize (if needed) the grid
    nW = GridW: nH = GridH
    If X + 1 > nW Then nW = X + 1
    If Y + 1 > nH Then nH = Y + 1
    ResizeGrid nW, nH
        
    InitCell X, Y
    
    ' Assign/load image
    Dim Img As StdPicture
    Set Img = Image
    
    '#@# DISABLED - ImgLib does the shit-work #@#
    'If Img Is Nothing Then Set Img = LoadPicture(BasePath & FileName)
    
    ' Check if the layer is used
    If Not Grid(X, Y).Used(Layer) Then
        ' It is not? Create a new image object
        Grid(X, Y).Used(Layer) = True
        Grid(X, Y).ImageFilenames(Layer) = FileName
        RaiseEvent AllocateImage(X * 32, Y * 32, Img, Grid(X, Y).ImageIndexes(Layer))
    Else
        ' It is? Then replace the old image object
        Grid(X, Y).ImageFilenames(Layer) = FileName
        RaiseEvent AlterImage(Grid(X, Y).ImageIndexes(Layer), Img)
    End If
    
    ' Do the proper z-ordering
    ReZOrder X, Y
End Sub

' Rearrange image objects to match layer order
Private Sub ReZOrder(ByVal X As Integer, ByVal Y As Integer)
    Dim i
    
    ' Used only on initialized cells
    If Not Grid(X, Y).Initialized Then Exit Sub

    ' Start sending image elements to back
    For i = 5 To 0 Step -1
        If Grid(X, Y).Used(i) Then RaiseEvent ZOrderBack(Grid(X, Y).ImageIndexes(i), X, Y)
    Next i
End Sub

' Z-Index Shifting
Public Sub ZShift(ByVal X As Integer, ByVal Y As Integer, ByVal Shift As Integer)
    Dim i, j
    Dim CacheI As Long, CacheF As String, CacheU As Boolean
    
    If Shift > 0 Then
        ' Right shifting
        For j = 1 To Shift
            ' Swapping items 1 to 5 performs a shift right
            For i = 0 To 4
                CacheI = Grid(X, Y).ImageIndexes(i)
                CacheF = Grid(X, Y).ImageFilenames(i)
                CacheU = Grid(X, Y).Used(i)
                
                Grid(X, Y).ImageIndexes(i) = Grid(X, Y).ImageIndexes(i + 1)
                Grid(X, Y).ImageFilenames(i) = Grid(X, Y).ImageFilenames(i + 1)
                Grid(X, Y).Used(i) = Grid(X, Y).Used(i + 1)
                
                Grid(X, Y).ImageIndexes(i + 1) = CacheI
                Grid(X, Y).ImageFilenames(i) = CacheF
                Grid(X, Y).Used(i + 1) = CacheU
            Next i
        Next j
    Else
        ' Left shifting
        For j = 1 To Shift
            ' Swapping items 1 to 5 in reverse order performs a shift left
            For i = 4 To 0 Step -1
                CacheI = Grid(X, Y).ImageIndexes(i)
                CacheF = Grid(X, Y).ImageFilenames(i)
                CacheU = Grid(X, Y).Used(i)
                
                Grid(X, Y).ImageIndexes(i) = Grid(X, Y).ImageIndexes(i + 1)
                Grid(X, Y).ImageFilenames(i) = Grid(X, Y).ImageFilenames(i + 1)
                Grid(X, Y).Used(i) = Grid(X, Y).Used(i + 1)
                
                Grid(X, Y).ImageIndexes(i + 1) = CacheI
                Grid(X, Y).ImageFilenames(i) = CacheF
                Grid(X, Y).Used(i + 1) = CacheU
            Next i
        Next j
    End If
    
    ' Rearrange ZOrder
    ReZOrder X, Y
End Sub

Private Sub ResizeGrid(ByVal ToW As Integer, ByVal ToH As Integer)
    Dim X, Y
    Dim HasGrid As Boolean
    Dim Cache() As Cell
    
    ' Drop if the same
    If (ToW = GridW) And (ToH = GridH) Then Exit Sub
    
    ' Create cache and copy grid to cache (if grid exists)
    HasGrid = (GridW > 0) And (GridH > 0)
    If HasGrid Then
        ReDim Cache(GridW - 1, GridH - 1)
        For X = 0 To GridW - 1
            For Y = 0 To GridH - 1
                Cache(X, Y) = Grid(X, Y)
            Next Y
        Next X
    End If
    
    ' Resize Base Grid
    ReDim Grid(ToW - 1, ToH - 1)
    
    ' Restore cache
    If HasGrid Then
        For X = 0 To GridW - 1
            For Y = 0 To GridH - 1
                Grid(X, Y) = Cache(X, Y)
            Next Y
        Next X
    End If
    
    ' Save to grid w/h
    GridW = ToW
    GridH = ToH
End Sub

' Erase the whole grid and reset system
Public Sub Truncate()
    ' Feed destruction events
    Dim X, Y, i
    For X = 0 To GridW - 1
        For Y = 0 To GridH - 1
        
            ' Is cell initialized?
            If Grid(X, Y).Initialized Then
            
                ' For each layer that contains an image, feed it's destruction
                For i = 0 To 5
                    If Grid(X, Y).Used(i) Then RaiseEvent DestroyImage(Grid(X, Y).ImageIndexes(i))
                Next i
            End If
        Next Y
    Next X

    ' Reset
    GridW = 0
    GridH = 0
    ReDim Grid(0, 0)
End Sub

Private Sub Class_Initialize()
    GridW = 0
    GridH = 0
    ReDim Grid(0, 0)
End Sub

' Get/Set the attenuation of a grid item
Public Property Get Attenuation(ByVal X As Integer, ByVal Y As Integer) As Integer
    ' Item off the grid? Attenuation maximum ..
    If (X >= GridW) Or (Y >= GridH) Then
        Attenuation = 10
        Exit Property
    End If
    
    ' Grid not initialized? Attenuation minimum
    If Not Grid(X, Y).Initialized Then
        Attenuation = 10
        Exit Property
    End If

    ' Else, get the attenuation
    Attenuation = Grid(X, Y).Attenuation
End Property
Public Property Let Attenuation(ByVal X As Integer, ByVal Y As Integer, ByVal vNewValue As Integer)
    Dim nW As Integer, nH As Integer
    
    ' Item off the grid? Resize grid
    If (X >= GridW) Or (Y >= GridH) Then
    
        ' Calculate and resize (if needed) the grid
        nW = GridW: nH = GridH
        If X + 1 > nW Then nW = X + 1
        If Y + 1 > nH Then nH = Y + 1
        ResizeGrid nW, nH
        
    End If

    ' Initialize the cell if not initialized
    InitCell X, Y

    ' Set attenuation
    Grid(X, Y).Attenuation = vNewValue
End Property

' Get/Set the elevation of a grid item
Public Property Get Elevation(ByVal X As Integer, ByVal Y As Integer) As Integer
    ' Item off the grid?
    If (X >= GridW) Or (Y >= GridH) Then
        Elevation = 0
        Exit Property
    End If
    
    ' Grid not initialized?
    If Not Grid(X, Y).Initialized Then
        Elevation = 0
        Exit Property
    End If

    ' Else, get the attenuation
    Elevation = Grid(X, Y).Elevation
End Property
Public Property Let Elevation(ByVal X As Integer, ByVal Y As Integer, ByVal vNewValue As Integer)
    Dim nW As Integer, nH As Integer
    
    ' Item off the grid? Resize grid
    If (X >= GridW) Or (Y >= GridH) Then
    
        ' Calculate and resize (if needed) the grid
        nW = GridW: nH = GridH
        If X + 1 > nW Then nW = X + 1
        If Y + 1 > nH Then nH = Y + 1
        ResizeGrid nW, nH
        
    End If

    ' Initialize the cell if not initialized
    InitCell X, Y

    ' Set attenuation
    Grid(X, Y).Elevation = vNewValue
End Property



' Trim or fill a value to match a specified length
Private Function Trunc(ByVal Value As String, ByVal Length As Integer, Optional FillChar = " ") As String
    If Len(Value) >= Length Then
        Trunc = Left(Value, Length)
    Else
        Trunc = Value & String(Length - Len(Value), FillChar)
    End If
End Function

' Save value to file
Public Sub SaveToFile(FileName As String)
    Dim s As String
    Dim l As Long
    l = FreeFile
    Open FileName For Output As #l
    Close #l
    Open FileName For Binary As #l
    
    ' Header : MIME Type
    Put #l, , "GMFv1"
    
    ' Grid Size [4b W][4b H] (Ranges: "0   " ~ "9999")
    Put #l, , Trunc(GridW, 4)
    Put #l, , Trunc(GridH, 4)
    
    ' Elevations [4b Min][4b Max]
    Put #l, , Trunc(CStr(0), 4) '[Unused]
    Put #l, , Trunc(CStr(5), 4) '[Unused]
    
    ' Background [50b]
    Put #l, , Trunc(BackgroundFilename, 40)
    
    ' Start saving layers
    Dim X, Y, i
    For X = 0 To GridW - 1
        For Y = 0 To GridH - 1
            
            ' Set Elevation
            Put #l, , Trunc(Grid(X, Y).Elevation, 2)
            ' Set Attenuation
            Put #l, , Trunc(Grid(X, Y).Attenuation, 2)  ' [Unused]
            
            For i = 0 To 4
                If Grid(X, Y).Initialized Then
                    Put #l, , Trunc(Grid(X, Y).ImageFilenames(i), 40)
                Else
                    Put #l, , Space$(40)
                End If
            Next i
        Next Y
    Next X
    
    ' End
    Close #l
End Sub

' Load from file
Public Sub LoadFromFile(FileName As String)
    Dim s As String
    Dim l As Long
    l = FreeFile
    Open FileName For Binary As #l
    
    ' Header : MIME Type
    s = Space$(5)
    Get #l, , s
    If s <> "GMFv1" Then
        Err.Raise &HFF01, , "This is not a valid GloryLands Map File!"
        Exit Sub
    End If
    
    ' Grid Size [4b W][4b H] (Ranges: "0   " ~ "9999")
    s = Space$(4)
    Get #l, , s
    GridW = CInt(Trim(s))
    Get #l, , s
    GridH = CInt(Trim(s))
    ReDim Grid(GridW, GridH)
    
    ' Background [50b]
    s = Space$(40)
    Get #l, , s
    BackgroundFilename = Trim(s)
    
    ' Start saving layers
    Dim X, Y, i
    Dim iUsed As Boolean
    For X = 0 To GridW - 1
        For Y = 0 To GridH - 1
            
            ' Get Attenuation
            s = Space$(2)
            Get #l, , s
            Grid(X, Y).Attenuation = CInt(Trim(s))
            
            ' Load and draw layers
            s = Space$(40)
            For i = 0 To 4
                Get #l, , s
                
                If Trim(s) <> "" Then
                    Me.SetImg X, Y, i, Trim(s)
                End If
            Next i
        Next Y
    Next X
    
    ' End
    Close #l
End Sub

' Get/Set an item's ZBuffer Index
Public Property Get ItemZ(ByVal X As Integer, ByVal Y As Integer, ByVal Layer As Byte) As Integer
    ' Item off the grid?
    If (X >= GridW) Or (Y >= GridH) Then
        ItemZ = 0
        Exit Property
    End If
    
    ' Grid not initialized?
    If Not Grid(X, Y).Initialized Then
        ItemZ = 0
        Exit Property
    End If

    ' Else, get the attenuation
    ItemZ = Grid(X, Y).Z(Layer)
End Property
Public Property Let ItemZ(ByVal X As Integer, ByVal Y As Integer, ByVal Layer As Byte, ByVal vNewValue As Integer)
    Dim nW As Integer, nH As Integer
    
    ' Item off the grid? Resize grid
    If (X >= GridW) Or (Y >= GridH) Then
    
        ' Calculate and resize (if needed) the grid
        nW = GridW: nH = GridH
        If X + 1 > nW Then nW = X + 1
        If Y + 1 > nH Then nH = Y + 1
        ResizeGrid nW, nH
        
    End If

    ' Initialize the cell if not initialized
    InitCell X, Y

    ' Set attenuation
    Grid(X, Y).Z(Layer) = vNewValue
End Property

' Get/Set an item's ZBuffer Index
Public Property Get ItemHeight(ByVal X As Integer, ByVal Y As Integer, ByVal Layer As Byte) As Integer
    ' Item off the grid?
    If (X >= GridW) Or (Y >= GridH) Then
        ItemHeight = 0
        Exit Property
    End If
    
    ' Grid not initialized?
    If Not Grid(X, Y).Initialized Then
        ItemHeight = 0
        Exit Property
    End If

    ' Else, get the attenuation
    ItemHeight = Grid(X, Y).Height(Layer)
End Property
Public Property Let ItemHeight(ByVal X As Integer, ByVal Y As Integer, ByVal Layer As Byte, ByVal vNewValue As Integer)
    Dim nW As Integer, nH As Integer
    
    ' Item off the grid? Resize grid
    If (X >= GridW) Or (Y >= GridH) Then
    
        ' Calculate and resize (if needed) the grid
        nW = GridW: nH = GridH
        If X + 1 > nW Then nW = X + 1
        If Y + 1 > nH Then nH = Y + 1
        ResizeGrid nW, nH
        
    End If

    ' Initialize the cell if not initialized
    InitCell X, Y

    ' Set attenuation
    Grid(X, Y).Height(Layer) = vNewValue
End Property

