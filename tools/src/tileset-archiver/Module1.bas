Attribute VB_Name = "Module1"
Option Explicit

Public Function FixPath(ByVal Src As String) As String
    If Right(Src, 1) = "\" Then FixPath = Src Else FixPath = Src & "\"
End Function
