VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "clsImgLib"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit

Dim Images As Collection

Private Sub Class_Initialize()
    Set Images = New Collection
End Sub

Private Sub Class_Terminate()
    Set Images = Nothing
End Sub

Public Sub AssignImage(ByVal ImageName As String)
    On Error GoTo NotExists:
    
    ' Format image name
    ImageName = Trim(Replace(ImageName, vbNullChar, ""))
    
    ' Exclude empty images
    If ImageName = "" Then Exit Sub
    
    ' Try to load it. If does not exist an error will occure
    ' If exists, there is no need for further loading
    Dim iPic As StdPicture
    Set iPic = Images(UCase(ImageName))
    Exit Sub
    
NotExists:
    
    ' Load data
    'Set iPic = LoadPicture(BasePath & ImageName)
    Set iPic = imgStorage.LoadImage(ImageName)
    'If iPic Is Nothing Then Exit Sub
    
    ' Store data
    Images.Add iPic, UCase(ImageName)
    
End Sub

Public Property Get Image(ByVal ImageName As String) As StdPicture
    On Error GoTo er
    
    ' Format image name
    ImageName = Trim(Replace(ImageName, vbNullChar, ""))
    
    ' Exclude empty images
    If ImageName = "" Then
        Set Image = Nothing
        Exit Property
    End If
    
    ' Try to load the image
    Set Image = Images(UCase(ImageName))
    Exit Property
er:

    ' Not exists? Try to create it
    AssignImage ImageName
    
    ' Try to load the image
    On Error GoTo er2
    Set Image = Images(UCase(ImageName))
    Exit Property
er2:
    
    ' Error again? It could not be loaded!
    Set Image = Nothing
End Property

Public Sub Clear()
    Set Images = Nothing
    Set Images = New Collection
End Sub
