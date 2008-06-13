VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "clsResizableArray"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

Dim TheArray()

Dim MaxX As Integer
Dim MaxY As Integer

Private Sub Class_Initialize()
    MaxX = 0
    MaxY = 0
End Sub

Public Property Get Value(ByVal X As Integer, ByVal Y As Integer) As Variant
Attribute Value.VB_UserMemId = 0
    If X >= MaxX Or Y >= MaxY Then
        Value = Null
    End If
    Value = TheArray(X, Y)
End Property

Public Property Let Value(ByVal X As Integer, ByVal Y As Integer, ByVal vNewValue As Variant)
    Dim doResize As Boolean
    Dim szW As Integer, szH As Integer
    
    ' Check if we need to resize the storage
    doResize = False: szW = MaxX: szH = MaxY
    If X >= szW Then
        szW = X + 1
        doResize = True
    End If
    If Y >= szH Then
        szH = Y + 1
        doResize = True
    End If
    If doResize Then Resize szW, szH
    
    ' Store the value
    TheArray(X, Y) = vNewValue
End Property

Public Sub Resize(ByVal ToX As Integer, ByVal ToY As Integer)
    Dim iX, iY, Tmp()
    
    ' If ToX is 0 or ToY is 0 the array is destroied
    If ToX = 0 Or ToY = 0 Then
        MaxX = 0
        MaxY = 0
        ReDim TheArray(0, 0)
        Exit Sub
    End If
    
    ' Store old array
    ReDim Tmp(MaxX - 1, MaxY - 1)
    For iX = 0 To MaxX - 1
        For iY = 0 To MaxY - 1
            Tmp(iX, iY) = TheArray(iX, iY)
        Next iY
    Next iX
    
    ' Resize the new array
    ReDim TheArray(ToX - 1, ToY - 1)
    
    ' Calculate transfer widths
    Dim NewX, NewY
    NewX = ToX
    If ToX > MaxX Then NewX = MaxX
    NewY = ToY
    If ToY > MaxY Then NewY = MaxY
    
    ' Copy the old buffer to the new buffer
    For iX = 0 To MaxX - 1
        For iY = 0 To MaxY - 1
            Tmp(iX, iY) = TheArray(iX, iY)
        Next iY
    Next iX

    ' Updadte width variables
    MaxX = ToX
    MaxY = ToY
End Sub

Private Sub Class_Terminate()
    ' Destroy array
    Resize 0, 0
End Sub

Public Property Get Width() As Integer
    Width = MaxX
End Property

Public Property Get Height() As Integer
    Height = MaxY
End Property

