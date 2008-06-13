Attribute VB_Name = "Module1"
Option Explicit

Private Sub Stack(ByVal cType As String, f As Long)
    Dim s As String
    s = Dir(App.Path & "\*." & cType)
    Do While s <> ""
        Print #f, UCase(s) & " 23 """ & s & """"
        s = Dir
    Loop
End Sub

Sub Main()
    Dim f As Long
    f = FreeFile
    Open App.Path & "\project.rcscript" For Output As #f
    Stack "html", f
    Stack "css", f
    Stack "js", f
    Stack "vbs", f
    Stack "jpeg", f
    Stack "jpg", f
    Stack "gif", f
    Stack "png", f
    Close #f
End Sub
