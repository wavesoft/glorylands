VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "clsPHPArray"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

' =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
' Emulation of PHP's Array structure using a fully object-oriented design
' (Preety good I must say :P)
' =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
'
' Redim array simply using:
'
'   Array(1) = "value"
'   Array("name") = "value"
'
' Or even:
'
'   Array(1)(2) = "sub levels!"
'   Array(1)("wow") = "no preinit!"
'
' You can also exaggerate without any problem ;)
'
'   Array(1)(2)("many")("dimensions")(4)(5)("WHoa!?") = "hehe"
'
' =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
' Some usefull functions included:
' =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
'
' 1] Create             : ex. A.Create(1,2,3,"item","test")   : Just like php's Array(...)
' 2] Push               : ex. A.Push("item")                  : Adds an item with auto-index
'                                                              (Do not use the optional "key" unless you
'                                                               know what are you doing!!!)
' 3] Pop                : ex. V = A.Pop()                     : Get and remove the last item
' 4] Remove             : ex. A.Remove 44                     : Remove an item (By Key and not by index!)
' 5] Reset              : ex. A.Reset                         : Full reset
' 6] SortByKey          : ex. A.SortByKey                     : Sort items using each item's key as index
' 7] Serialze           : ex. A.Serialize                     : Just like PHP's serialize function
' 8] StructPrint        : ex. Msgbox A.StructPrint()          : Visual structure print (like print_r)
'
' =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
'  NOTE: Serialization currently does not support:
'        1) Recursive inclusions
'        2) Decimal numbers
'
' =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
'
' (C) 2007, John Haralampidis - Wavesoft (johnys2@gmail.com)
'     All Rights reserved
'
' =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

Dim Keys As Collection
Dim Values As Collection
Dim myUBound As Long
Dim myLBound As Long

Public Property Get ItemCount() As Integer
    ItemCount = Values.Count
End Property

Public Property Get ItemValue(ByVal Index As Integer)
    ItemValue = Values(Index)
End Property

Public Property Get ItemKey(ByVal Index As Integer)
    ItemKey = Keys(Index)
End Property

Public Sub Create(ParamArray iValues())
    Set Keys = Nothing
    Set Values = Nothing
    Set Keys = New Collection
    Set Values = New Collection
    Dim q
    For Each q In iValues
        Push q
    Next q
End Sub

Public Property Get Value(Optional ByVal Key As Variant = Null) As Variant
Attribute Value.VB_UserMemId = 0
    Dim i As Integer
    
    ' If no key is specified, return the first available entry
    If IsNull(Key) Then
        If Values.Count <= 0 Then Exit Property
        Value = Values(1)
        Exit Property
    End If
    
    ' If "_self" specified, return myself
    If Key = "_self" Then
        Set Value = Me
        Exit Property
    End If
    
    ' If found, return the object
    For i = 1 To Keys.Count
        'If Keys(i) = UCase(Key) Then
        If Keys(i) = CStr(Key) Then
            If IsObject(Values(i)) Then
                Set Value = Values(i)
            Else
                Value = Values(i)
            End If
            Exit Property
        End If
    Next i
    
    ' If not found, create a new, blank instance and return it
    Dim iObj As New clsPHPArray
    Set Value = iObj
    
    ' And then store it on our system
    Push iObj, Key
End Property

Public Property Let Value(Optional ByVal Key As Variant = Null, ByVal vNewValue As Variant)
    Dim i As Integer
    If Information.IsEmpty(vNewValue) Then Exit Property
    
    ' If no key is specified, set the first available entry
    If IsNull(Key) Then
        If Values.Count <= 0 Then
            ' Not exists? Create the first entry
            Push vNewValue
        Else
            ' Exists? replace the first value
            Values.Add vNewValue, , , 1
            Values.Remove 1
        End If
        Exit Property
    End If
    
    ' If the key exists, replace the value
    For i = 1 To Keys.Count
        'If Keys(i) = UCase(Key) Then
        If Keys(i) = CStr(Key) Then
            Values.Add vNewValue, , , i
            Values.Remove i
            Exit Property
        End If
    Next i
    
    ' If the key does not exists, append a new entry
    Push vNewValue, Key
End Property

Public Sub Push(Value, Optional Key = Null)
    Dim iKey
    
    If IsNull(Key) Then
    
        ' Set Key count+1 as key ID
        iKey = Keys.Count + 1
        
        ' Make sure the key is unique
        Dim i
        For i = 1 To Keys.Count
            If IsNumeric(Keys(i)) Then
                If Keys(i) > iKey Then
                    iKey = Keys(i) + 1
                End If
            End If
        Next i
    
    Else
        ' [!!] Warning [!!] There is no check if the Key pre-exists
        iKey = Key
    End If

    ' Update ubound/lbound
    If IsNumeric(iKey) Then
        If iKey < myLBound Then myLBound = iKey
        If iKey > myUBound Then myUBound = iKey
    End If

    ' Append key & value
'    Keys.Add UCase(iKey)
    Keys.Add CStr(iKey)
    Values.Add Value
End Sub

Public Sub Remove(Item)
    If Values.Count <= 0 Then Exit Sub
    
    ' Remove value based on key (only)
    Dim i As Integer
    For i = 1 To Keys.Count
'        If Keys(i) = UCase(Item) Then
        If Keys(i) = CStr(Item) Then
            Values.Remove i
            Keys.Remove i
        End If
    Next i
End Sub

Public Function Pop() As Variant
    If Values.Count <= 0 Then Exit Function
    Pop = Values(Values.Count)
    Keys.Remove Values.Count
    Values.Remove Values.Count
End Function

Private Sub Class_Initialize()
    Set Keys = New Collection
    Set Values = New Collection
End Sub

Public Function StructPrint() As String
    Dim i
    StructPrint = "Array(" & vbCrLf
    For i = 1 To Values.Count
        StructPrint = StructPrint & "    " & "[" & Keys(i) & "] => "
        If IsObject(Values(i)) Then
            StructPrint = StructPrint & Replace(Values(i).StructPrint, vbCrLf, vbCrLf & "    ")
        Else
            StructPrint = StructPrint & Values(i)
        End If
        StructPrint = StructPrint & vbCrLf
    Next i
    StructPrint = StructPrint & ")"
End Function

' Quick String Serialize
Private Function QSS(ByVal s As String) As String
    QSS = "s:" & Len(s) & ":""" & s & """"
End Function

' Quick Var Serialize
Private Function QVS(ByVal s) As String
    If IsNumeric(s) Then
        QVS = "i:" & s
    Else
        QVS = QSS(s)
    End If
End Function

Public Function Serialize() As String
    Dim i
    ' Array(<size>) {
    Serialize = "a:" & Values.Count & ":{"
    For i = 1 To Values.Count

        ' [<Key>] =>
        Serialize = Serialize & QVS(Keys(i)) & ";"

        ' <Value>
        If IsObject(Values(i)) Then
            '(!) Function Returns "}" in the end, that is same like ";". So & ";" is not needed
            Serialize = Serialize & Values(i).Serialize
        Else
            Serialize = Serialize & QVS(Values(i)) & ";"
        End If
    Next i
    
    ' }
    Serialize = Serialize & "}"
End Function

' JSON Value format
Private Function JVF(Value) As String
    If IsNumeric(Value) Then
        JVF = CStr(Value)
    Else
        JVF = """" & Replace(Value, """", "\""") & """"
    End If
End Function

' JSON Key format
Private Function JKF(Value) As String
    JKF = """" & Replace(Value, """", "\""") & """"
End Function

Public Function JSONSerialize() As String
    Dim i
    ' {
    JSONSerialize = "{"
    For i = 1 To Values.Count

        ' <key>:
        JSONSerialize = JSONSerialize & JKF(Keys(i)) & ":"
    
        ' <Value>
        If IsObject(Values(i)) Then
            '(!) Function Returns "}" in the end, that is same like ";". So & ";" is not needed
            JSONSerialize = JSONSerialize & Values(i).JSONSerialize
        Else
            JSONSerialize = JSONSerialize & JVF(Values(i))
        End If
        If (i < Values.Count) Then JSONSerialize = JSONSerialize & ","
    Next i
    
    ' }
    JSONSerialize = JSONSerialize & "}"
End Function

Public Property Get Max() As Integer
    Max = myUBound
End Property

Public Property Get Min() As Integer
    Min = myLBound
End Property

Public Sub Reset()
    Set Keys = Nothing
    Set Values = Nothing
    Set Keys = New Collection
    Set Values = New Collection
End Sub

Private Sub Class_Terminate()
    Set Keys = Nothing
    Set Values = Nothing
End Sub

Public Sub SortByKey()
    ' (Simple, Bubblesort)
    Dim Temp As Variant, X As Integer, Sorted As Boolean
    Sorted = False
    Do While Not Sorted
        Sorted = True
        For X = 1 To Keys.Count - 1
            If Keys(X) > Keys(X + 1) Then
                ' Swap X & X+1
                Keys.Add Keys(X), , , X + 1
                Keys.Remove X
                Values.Add Values(X), , , X + 1
                Values.Remove X
                
                Sorted = False
            End If
        Next X
    Loop
End Sub

Private Function GetItemID(ByVal Key As String) As Integer
    Dim s As String
    Dim i
'    s = UCase(Trim(Key))
    s = Trim(Key)
    For i = 1 To Keys.Count
        If Keys(i) = s Then
            GetItemID = i
            Exit Function
        End If
    Next i
End Function

Public Function IsValue(Optional SubKey = Null) As Boolean
    If IsNull(SubKey) Then
        If Values.Count = 1 Then
            IsValue = (Keys(1) = 0)
        Else
            IsValue = False
        End If
    Else
        Dim i
        i = GetItemID(SubKey)
        IsValue = Values(i).IsValue
    End If
End Function

Public Function IsEmpty() As Boolean
    IsEmpty = (Values.Count = 0)
End Function

Private Sub SaveString(f As Long, s As String)
    Dim i As Integer
    i = Len(s)
    Put #f, , i
    Put #f, , s
End Sub

Private Function LoadString(f As Long) As String
    Dim i As Integer
    Get #f, , i
    LoadString = Space$(i)
    Get #f, , LoadString
End Function

Private Sub SaveType(f As Long, ByRef Value)
    Dim b As Byte, l As Long
    If IsNumeric(Value) Then
        b = 1
        l = Value
        Put #f, , b
        Put #f, , l
    Else
        b = 2
        Put #f, , b
        SaveString f, CStr(Value)
    End If
End Sub

Private Sub SaveObject(f As Long, ByRef Obj As clsPHPArray)
    Dim b As Byte
    b = 3
    Put #f, , b
    Obj.SaveToFile f
End Sub

Private Function LoadType(f As Long) As Variant
    Dim b As Byte, l As Long, s As String, iObj As clsPHPArray
    Get #f, , b
    If b = 1 Then
        Get #f, , l
        LoadType = l
    ElseIf b = 3 Then
        Set LoadType = New clsPHPArray
        LoadType.LoadFromFile f
    ElseIf b = 2 Then
        LoadType = LoadString(f)
    End If
End Function

Public Function SaveToFile(ByVal hFile As Long)
    Dim i As Integer
    i = Values.Count
    
    ' Put the number of entries included
    Put #hFile, , i
    
    ' Put our key/values
    For i = 1 To Values.Count
        SaveType hFile, Keys(i)
        If IsObject(Values(i)) Then
            SaveObject hFile, Values(i)
        Else
            SaveType hFile, Values(i)
        End If
    Next i
End Function

Public Sub LoadFromFile(ByVal hFile As Long)
    Dim i As Integer, l As Integer, K, V
    ' Reset file
    Reset
    
    ' Put the number of entries included
    Get #hFile, , l
    
    ' Put our key/values
    For i = 1 To l
        K = LoadType(hFile)
        Me.Push LoadType(hFile), K
    Next i
End Sub
