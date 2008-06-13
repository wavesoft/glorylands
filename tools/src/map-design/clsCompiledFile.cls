VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "clsCompiledFile"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit
Public BuildMode As enmCompileModes
Dim hDesigner As clsMapDesigner
Dim hFile As String

' BuildMode: PHP Binary ------------------
Dim binGrid As clsPHPArray
Dim binMode As Byte

' BuildMode: Chunk Mode ------------------
Public ChunkWidth As Integer
Public ChunkHeight As Integer
Dim MaxW As Integer, MaxH As Integer

Public Sub ParseDesigner(ByVal mDesigner As clsMapDesigner, ByVal FileName As String)
    Dim f As Long
    Dim i
    
    frmSplash.Label1.Caption = "Compiling map file. Please wait..."
    frmSplash.Show
    DoEvents
    
    MaxW = 0
    MaxH = 0
    Set hDesigner = mDesigner
    f = FreeFile
    hFile = FileName
    
    ' Mode #1 (Scriptable PHP object)
    If BuildMode = cmPHP Then
        Open hFile For Output As #f
        Print #f, "<?php"
        Print #f, "// This is an automatically generated file to build a specific map file"
        Close #f
        
        For i = 1 To mDesigner.ObjectCount
            ' Request every object to send the information on me
            frmMain.StatusBar1.Panels(1).Text = "Compiling object #" & i
            DoEvents
            mDesigner.Object(i).BuildCompileChunk Me
        Next i
        
        f = FreeFile
        Open hFile For Append As #f
        Print #f, "?>"
        Close #f
    
    ' Mode #2 (PHP Serialized array)
    ElseIf BuildMode = cmSerialized Or BuildMode = cmChunk Or BuildMode = cmSplit Then
        ' Choose mode
        If BuildMode = cmSerialized Then
            frmBD_CMP.Show 1
            binMode = frmBD_CMP.cMode
            Unload frmBD_CMP
        ElseIf BuildMode = cmChunk Then
            frmBD_CMMP.Show 1
            ChunkWidth = Val(frmBD_CMMP.Text1.Text)
            ChunkHeight = Val(frmBD_CMMP.Text2.Text)
            Unload frmBD_CMMP
        End If
    
        ' Initialize grid
        Set binGrid = New clsPHPArray
        
        ' Chunk model and split model uses the chunk provided from serialized mode Lv.3
        If BuildMode = cmChunk Then binMode = 3
        If BuildMode = cmSplit Then binMode = 3
        
        ' Request every object to send the information on me
        For i = 1 To mDesigner.ObjectCount
            frmMain.StatusBar1.Panels(1).Text = "Compiling object #" & i
            DoEvents
            mDesigner.Object(i).BuildCompileChunk Me
        Next i
        
        ' (Level 2 Processing)
        If binMode >= 2 Then
            ' Flatten result
            frmMain.StatusBar1.Panels(1).Text = "Flattenning layers..."
            DoEvents
            bmPHP_flatten
        End If
        
        If BuildMode = cmChunk Then
            
            ' Break the chunks
            frmMain.StatusBar1.Panels(1).Text = "Breaking down chunks..."
            DoEvents
            
            BuildSeparateGridFile
        
        ElseIf BuildMode = cmSplit Then
            ' Save result array in serialized representation
            frmMain.StatusBar1.Panels(1).Text = "Serializing file..."
            DoEvents
            
            BuildSplitGridFiles
            
        Else
            ' Save result array in serialized representation
            frmMain.StatusBar1.Panels(1).Text = "Serializing file..."
            DoEvents
            
            f = FreeFile
            Open hFile For Output As #f
            Print #f, binGrid.Serialize
            Close #f
        End If
    
    End If
    
    frmMain.StatusBar1.Panels(1).Text = "Compilation completed!"
    DoEvents
    
    Unload frmSplash
End Sub

Public Sub PutTileOnGrid(ByVal X As Integer, ByVal Y As Integer, ByVal Z As Integer, Layers() As String)
    Dim q, i, s As String
    Dim Ans As String
    
    If X > MaxW Then MaxW = X
    If Y > MaxH Then MaxH = Y
    
    ' Mode #1 (Scriptable PHP object)
    If BuildMode = cmPHP Then
        i = 0
        s = ""
        For Each q In Layers
            If Trim(q) <> "" Then
                If LCase(Right(Trim(q), 4)) <> ".gif" Then q = Trim(q) & ".gif"
                Ans = Ans & "push_layer(" & X & "," & Y & "," & Z & ",'" & q & "');"
            End If
            i = i + 1
        Next q
        
        Dim f As Long
        f = FreeFile
        Open hFile For Append As #f
        Print #f, Ans
        Close #f
        
    ' Mode #2 (PHP Serialized array)
    ElseIf BuildMode = cmSerialized Or BuildMode = cmChunk Or BuildMode = cmSplit Then
        For Each q In Layers
            q = Trim(q)
            If q <> "" Then
                If LCase(Right(q, 4)) <> ".gif" Then q = q & ".gif"
                bmPHP_push_layer X, Y, Z, q
            End If
        Next q
        
    End If
End Sub

'' ===[ VB Migration of PHP Merging system ]==================================================================

Private Sub bmPHP_push_layer(ByVal X As Integer, ByVal Y As Integer, ByVal Z As Integer, ByVal Image As String)

    ' Original:
    'function push_layer($x,$y,$z,$image) {
    '   global $grid;
    '   if (!isset($grid[$x][$y][$z])) $grid[$x][$y][$z] = array();
    '   array_push($grid[$x][$y][$z], $image);
    '}

    binGrid(X)(Y)(Z).Push Image
End Sub

Private Sub bmPHP_flatten()

    ' Original:
    'function flatten() {
    '    // Complexity O(N^4) ?!?!?!!?
    '
    '    global $grid;
    '    $ngrid = array();
    '    foreach ($grid as $y => $y_grid) {
    '        foreach ($y_grid as $x => $z_grid) {
    '            $items = array();
    '            reset($items);
    '            foreach ($z_grid as $z => $img) {
    '                foreach ($img as $id => $file) {
    '                    $depth = $z * 10 + $id;
    '                    $items[$depth] = $file;
    '                }
    '            }
    '            ksort($items);
    '            $ngrid[$x][$y] = $items;
    '        }
    '    }
    '    $grid = $ngrid;
    '}

    Debug.Print "=[ Flatten Layers ]=========================================================="
    Debug.Print "[FIX] Algoritm complexity: almost O(N^4)!"
    Dim ngrid As New clsPHPArray, items As New clsPHPArray
    Dim X, Y, Z, Id, Depth As Integer, V
    frmSplash.ProgressBar1.Max = binGrid.Max
    For X = binGrid.Min To binGrid.Max
        For Y = binGrid(X).Min To binGrid(X).Max
            ' If instance is not empty, create new
            Set items = New clsPHPArray
            ' [!!] WARNING [!!]
            '  VB Works with INSTANCES not with real object data. That means
            '  that even if we assign ngrid(x)(y) the object inside "items" variable, the handle
            '  in "items" variable still points also on the ngrid(x)(y) location!
            '  So, instead of cleaning up "items" variable (That will allso erase all the previous
            '  instances stored on ngrid(x)(y) somewere), we reconstruct it.
            
            For Z = binGrid(X)(Y).Min To binGrid(X)(Y).Max
                If Not binGrid(X)(Y)(Z).IsEmpty Then
                    If Not binGrid(X)(Y)(Z).IsEmpty Then
                        For Id = binGrid(X)(Y)(Z).Min To binGrid(X)(Y)(Z).Max
                                If binGrid(X)(Y)(Z)(Id) <> "" Then
                                    Depth = Z * 10 + Id
                                    items(Depth) = binGrid(X)(Y)(Z)(Id)
                                End If
                        Next Id
                    End If
                End If
            Next Z
            items.SortByKey
            If binMode = 3 Then
                ngrid(Y)(X)("I") = items
                V = hDesigner.AttGrid(X)(Y)
                If Not IsObject(V) And Not IsEmpty(V) Then
                    ngrid(Y)(X)("Z") = V
                End If
            Else
                ngrid(Y)(X) = items
            End If
        Next Y
        frmSplash.ProgressBar1.Value = X
    Next X
    Set binGrid = Nothing
    Set binGrid = ngrid
End Sub

'' ===[ Chunk model separations system ]======================================================================

Private Sub RectBuilid(ByVal Left As Integer, ByVal Top As Integer, ByVal Right As Integer, ByVal Bottom As Integer, ByVal FileName As String)
    Dim iGrid As New clsPHPArray
    Dim X, Y, V
    
    ' Initialize an empty Z Buffer
    iGrid.Push New clsPHPArray, "I"
    iGrid.Push New clsPHPArray, "Z"
    
    'MsgBox iGrid.StructPrint
    
    Debug.Print " @R("; Left; Top; Right; Bottom; ")", "Make...";
    For Y = Top To Bottom
        For X = Left To Right
            If Not binGrid(Y)(X)("I")("_self").IsEmpty Then
                iGrid("I")(Y)(X) = binGrid(Y)(X)("I")
            End If
            V = binGrid(Y)(X)("Z")
            If Not IsEmpty(V) And Not IsObject(V) Then
                iGrid("Z")(Y)(X) = V
            End If
        Next X
    Next Y
    
    'frmStructView.Text1.Text = iGrid.StructPrint
    'frmStructView.Show 1
    
    Debug.Print "Write...";
    Dim f As Long
    f = FreeFile
    Open FileName & ".gcmp" For Output As #f
    Print #f, iGrid.Serialize
    Close #f
    Debug.Print "Ok ";
End Sub

Private Function BuildSeparateGridFile()
    Dim X, Y
    Debug.Print "=[ Building Sparse files ]==================================================="
    Debug.Print "Sparse Width: "; ChunkWidth; " / Height: "; ChunkHeight
    frmSplash.ProgressBar1.Max = MaxH
    For Y = 0 To MaxH Step ChunkHeight
        frmSplash.ProgressBar1.Value = Y
        For X = 0 To MaxW Step ChunkWidth
            frmMain.StatusBar1.Panels(1).Text = "Grid part " & X & "," & Y & "..."
            DoEvents
            Debug.Print "Block: "; X; Y; (X + ChunkWidth); (X + ChunkHeight), ;
            RectBuilid X, Y, X + ChunkWidth - 1, Y + ChunkHeight - 1, hFile & "." & X & "." & Y
            Debug.Print "[mapfile." & X & "." & Y & ".gcmp]"
        Next X
    Next Y
End Function

Private Function BuildSplitGridFiles()
    Dim iGrid As New clsPHPArray
    Dim iDictionary As New clsPHPArray
    Dim X, Y, V, i, K, dicEntry As Integer, dID As Integer
    
    ' Initialize an empty Z Buffer
    dicEntry = 0
    iGrid.Push New clsPHPArray, "I"
    iGrid.Push New clsPHPArray, "Z"
    
    'MsgBox iGrid.StructPrint
    
    'frmStructView.Text1.Text = binGrid.StructPrint()
    'frmStructView.Show
    
    Debug.Print " @R("; 0; 0; MaxW; MaxH; ")", "Make...";
    frmSplash.ProgressBar1.Max = MaxH
    For Y = 0 To MaxH
        frmSplash.ProgressBar1.Value = Y
        For X = 0 To MaxW
            If Not binGrid(Y)(X)("I")("_self").IsEmpty Then
                For i = 1 To binGrid(Y)(X)("I").ItemCount
                    K = binGrid(Y)(X)("I").ItemKey(i)
                    V = binGrid(Y)(X)("I").ItemValue(i)
                    If IsEmpty(iDictionary(V)) Then
                        dID = dicEntry
                        iDictionary(V) = dicEntry
                        dicEntry = dicEntry + 1
                    Else
                        dID = iDictionary(V)
                    End If
                    Debug.Print "Grid ("; Y; X; "'I' "; K; ") = "; dID; " / "; V
                    iGrid("I")(Y)(X)(K) = dID
                Next i
            End If
            V = binGrid(Y)(X)("Z")
            If Not IsEmpty(V) And Not IsObject(V) Then
                iGrid("Z")(Y)(X) = V
            End If
        Next X
    Next Y
    
    'frmStructView.Text1.Text = iGrid.StructPrint
    'frmStructView.Show
    
    Dim DumpGrid As New clsPHPArray
    DumpGrid.Push iGrid("I"), "grid"
    DumpGrid.Push iDictionary, "dic"
    DumpGrid("range")("x")("m") = 0
    DumpGrid("range")("y")("m") = 0
    DumpGrid("range")("x")("M") = MaxW + 1
    DumpGrid("range")("y")("M") = MaxH + 1
    
    Debug.Print "Write...";
    Dim f As Long
    f = FreeFile
    Open hFile & ".jmap" For Output As #f
    Print #f, DumpGrid.JSONSerialize
    Close #f
    Open hFile & ".zmap" For Output As #f
    Print #f, iGrid("Z").Serialize
    Close #f
    Debug.Print "Ok ";

End Function
