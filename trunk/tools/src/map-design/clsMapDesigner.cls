VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "clsMapDesigner"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

Private Declare Function LockWindowUpdate Lib "user32" (ByVal hwndLock As Long) As Long

Public ImageGrid As clsImgGridObj
Public BackgroundImage As String
Public AttGrid As clsPHPArray
Dim Objects As Collection
Dim CurRegion As RGNex
Dim hParent As Long

Dim vMaxW As Integer, vMaxH As Integer

Public Sub SetVisibleRegion(ByVal Left As Integer, ByVal Top As Integer, ByVal Width As Integer, ByVal Height As Integer)
    ImageGrid.Truncate
    ImageGrid.PenAction Left + Width - 1, Top + Height - 1, 1, "", frmMain.Icon
    CurRegion.Left = Left
    CurRegion.Top = Top
    CurRegion.Width = Width
    CurRegion.Height = Height
    CurRegion.Right = Left + Width - 1
    CurRegion.Bottom = Top + Height - 1
End Sub

Public Sub PutObject(ByRef Obj As clsObjInstance)
    Dim i
    For i = 1 To Objects.Count
        If Obj Is Objects(i) Then Exit Sub
    Next i
    Objects.Add Obj
    Obj.BindTo Me
End Sub

Public Sub DeleteObject(ByRef Obj As clsObjInstance)
    Dim i
    For i = 1 To Objects.Count
        If Obj Is Objects(i) Then
            Objects.Remove i
            Exit Sub
        End If
    Next i
End Sub

Public Sub Init(ByRef ImgGrid As clsImgGridObj, ByVal ParentHWND As Long)
    Set ImageGrid = ImgGrid
    ImgGrid.Truncate
    hParent = ParentHWND
End Sub

Private Sub Class_Initialize()
    Set Objects = New Collection
    Set AttGrid = New clsPHPArray
End Sub

Public Sub Refresh()
    LockWindowUpdate hParent
    ImageGrid.Truncate
    DrawRegionObjects
    LockWindowUpdate 0
End Sub

Public Function ObjectIdFromPoint(ByVal X As Integer, ByVal Y As Integer) As Integer
    Dim i
    For i = Objects.Count To 1 Step -1
        If (X >= Objects(i).Left) And (X <= Objects(i).Right) And _
           (Y >= Objects(i).Top) And (Y <= Objects(i).Bottom) Then
           
            ObjectIdFromPoint = i
            Exit Function
        End If
    Next i
    ObjectIdFromPoint = 0
End Function

Private Sub DrawRegionObjects()
    Dim i
    Dim rL, rT, rW, rH, C
    
    ' Reset extents
    vMaxW = 0
    vMaxH = 0
    
    C = 0
    For i = 1 To Objects.Count
        ' Even if it is visible or not, calculate the grid extents
        If Objects(i).Right > vMaxW Then vMaxW = Objects(i).Right
        If Objects(i).Bottom > vMaxH Then vMaxH = Objects(i).Bottom
        
        ' 1) First 6 Lines performs the first check : If the four corners of the object
        '    are inside the visible area
        ' 2) The other 6 Lines performs the second (reverse) check : If the four corners of the region
        '    are inside the object
        If ((((Objects(i).Left >= CurRegion.Left) And (Objects(i).Left <= CurRegion.Right)) Or _
             ((Objects(i).Right >= CurRegion.Left) And (Objects(i).Right <= CurRegion.Right))) And _
             ((Objects(i).Top >= CurRegion.Top) And (Objects(i).Top <= CurRegion.Bottom)) Or _
            (((Objects(i).Left >= CurRegion.Left) And (Objects(i).Left <= CurRegion.Right)) Or _
             ((Objects(i).Right >= CurRegion.Left) And (Objects(i).Right <= CurRegion.Right))) And _
             ((Objects(i).Bottom >= CurRegion.Top) And (Objects(i).Bottom <= CurRegion.Bottom))) Or _
           ((((CurRegion.Left >= Objects(i).Left) And (CurRegion.Left <= Objects(i).Right)) Or _
             ((CurRegion.Right >= Objects(i).Left) And (CurRegion.Right <= Objects(i).Right))) And _
             ((CurRegion.Top >= Objects(i).Top) And (CurRegion.Top <= Objects(i).Bottom)) Or _
            (((CurRegion.Left >= Objects(i).Left) And (CurRegion.Left <= Objects(i).Right)) Or _
             ((CurRegion.Right >= Objects(i).Left) And (CurRegion.Right <= Objects(i).Right))) And _
             ((CurRegion.Bottom >= Objects(i).Top) And (CurRegion.Bottom <= Objects(i).Bottom))) _
            Then
            
            C = C + 1
            
            ' Initialize Region
            rW = Objects(i).Width
            rH = Objects(i).Height
            rL = 0
            rT = 0
            
            ' Find out the visible region
            If Objects(i).Left < CurRegion.Left Then
                rL = CurRegion.Left - Objects(i).Left
                rW = rW - (Objects(i).Left - CurRegion.Left)
            End If
            If Objects(i).Top < CurRegion.Top Then
                rT = CurRegion.Top - Objects(i).Top
                rH = rH - (Objects(i).Top - CurRegion.Top)
            End If
            If Objects(i).Right > CurRegion.Right Then
                rW = rW - (Objects(i).Right - CurRegion.Right)
            End If
            If Objects(i).Bottom > CurRegion.Bottom Then
                rH = rH - (Objects(i).Bottom - CurRegion.Bottom)
            End If
            
            ' If region has a visible width, display the object
            If (rH > 0) And (rH > 0) Then Objects(i).BlitTo rL, rT, rW, rH, CurRegion.Left, CurRegion.Top
            
        End If
    Next i
    
    Debug.Print C; " visible objects"
End Sub

Public Property Get Object(ByVal Id As Integer) As clsObjInstance
    On Error GoTo er
    Set Object = Objects(Id)
    Exit Property
er:
    Set Object = Nothing
End Property

Public Property Get ObjectCount() As Long
    ObjectCount = Objects.Count
End Property

Public Sub BringToFront(ByVal ObjectID As Integer)
    Dim hObj As clsObjInstance
    Set hObj = Objects(ObjectID)
    Objects.Remove ObjectID
    Objects.Add hObj
End Sub

Public Sub SendToBack(ByVal ObjectID As Integer)
    Dim hObj As clsObjInstance
    Set hObj = Objects(ObjectID)
    Objects.Remove ObjectID
    Objects.Add hObj, , 1
End Sub

Public Sub SaveDesigner(ByVal File As String)
    Dim l As Long, i
    l = Objects.Count
    
    Dim f As Long
    f = FreeFile
    Open File For Output As #f
    Close #f
    Open File For Binary As #f
    Put #f, , "MDAP1"
    
    ' Save background
    Dim Buf As String * 32
    Buf = BackgroundImage
    Put #f, , Buf
    
    ' Save Tiles
    Put #f, , l
    For i = 1 To l
        Objects(i).SaveChunk f
    Next i
    
    ' Save Attennuation grid
    AttGrid.SaveToFile f
    
    Close #f
End Sub

Public Sub LoadDesigner(ByVal File As String)
    Dim i, f As Long, l As Long, s As String, bLen As Byte, lLen As Long, o As New clsObjIO, iObj As clsObjInstance
    
    frmSplash.Label1.Caption = "Loading saved file. Please wait..."
    frmSplash.Show
    DoEvents
    
    ' Clean the grid
    l = Objects.Count
    For i = l To 1 Step -1
        Objects.Remove i
    Next i
    

    ' Read and build the file
    f = FreeFile
    Open File For Binary As #f
    s = Space$(5)
    Get #f, , s
    If s <> "MDAP1" Then
        Close #f
        Err.Raise &HFF05, "Map Designer", "This is not a valid map designer file!"
        Exit Sub
    End If
    
    ' Load background
    Dim Buf As String * 32
    Get #f, , Buf
    BackgroundImage = Trim(Buf)
    
    ' Load tiles
    Get #f, , l
    frmSplash.ProgressBar1.Max = l
    For i = 1 To l
        
        ' Get the instance file
        Get #f, , bLen
        s = Space$(bLen)
        Get #f, , s
        
        ' Load the file
        o.LoadFile FixPath(App.Path) & "\objects\" & s
        Set iObj = o.CreateInstance()
        
        ' Create the instalce
        iObj.BuildFromSaveChunk f
        
        ' Put instance on grid
        PutObject iObj
    
        frmSplash.ProgressBar1.Value = i
    
    Next i
    
    ' Load Attennuation grid
    AttGrid.LoadFromFile f
    
    Close #f
    Unload frmSplash
End Sub

Public Property Get MaxWidth() As Integer
    MaxWidth = vMaxW
End Property

Public Property Get MaxHeight() As Integer
    MaxHeight = vMaxH
End Property

