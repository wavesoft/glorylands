VERSION 5.00
Begin VB.UserControl ctlFuzzySlider 
   BackColor       =   &H00000000&
   ClientHeight    =   3600
   ClientLeft      =   0
   ClientTop       =   0
   ClientWidth     =   4800
   ScaleHeight     =   3600
   ScaleWidth      =   4800
   Begin VB.Shape Shape1 
      BorderColor     =   &H0000FF00&
      FillStyle       =   0  'Solid
      Height          =   375
      Left            =   0
      Top             =   0
      Width           =   255
   End
   Begin VB.Label Label1 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "Attennuation :"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   8.25
         Charset         =   161
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   255
      Left            =   600
      TabIndex        =   0
      Top             =   360
      Width           =   3495
   End
   Begin VB.Shape Shape2 
      BorderColor     =   &H00C0C0C0&
      Height          =   135
      Left            =   0
      Top             =   120
      Width           =   3615
   End
End
Attribute VB_Name = "ctlFuzzySlider"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = False
Option Explicit
Dim pValue As Integer

'Default Property Values:
Const m_def_Min = 0
Const m_def_Max = 100
Const m_def_Value = 0
Const m_def_Units = "%"
Const m_def_Caption = ""

'Property Variables:
Dim m_Min As Single
Dim m_Max As Single
Dim m_Value As Single
Dim m_Units As String
Dim m_Caption As String

Private Sub UserControl_MouseDown(Button As Integer, Shift As Integer, X As Single, Y As Single)
    Shape1.BorderColor = &HFF&
    UserControl_MouseMove Button, Shift, X, Y
End Sub

Private Sub UserControl_MouseMove(Button As Integer, Shift As Integer, X As Single, Y As Single)
    Dim lP As Integer, mW As Integer
    
    If Button = 1 Then
        lP = X - (Shape1.Width / 2)
        If lP < 0 Then lP = 0
        If lP + Shape1.Width > UserControl.Width Then lP = UserControl.Width - Shape1.Width
        
        mW = UserControl.Width - Shape1.Width
        m_Value = ((m_Max - m_Min) * lP / mW) + m_Min
        
        lP = lP \ 60
        Shape1.Left = lP * 60
        
        Label1.Caption = m_Caption & " " & Fix(m_Value) & " " & m_Units
    End If
End Sub

Private Sub UserControl_MouseUp(Button As Integer, Shift As Integer, X As Single, Y As Single)
    Shape1.BorderColor = &HFF00&
End Sub

Private Sub UserControl_Resize()
    Shape2.Width = UserControl.Width
    Label1.Left = 0
    Label1.Width = UserControl.Width
End Sub

'WARNING! DO NOT REMOVE OR MODIFY THE FOLLOWING COMMENTED LINES!
'MemberInfo=12,0,0,0
Public Property Get Min() As Single
    Min = m_Min
End Property

Public Property Let Min(ByVal New_Min As Single)
    m_Min = New_Min
    PropertyChanged "Min"
End Property

'WARNING! DO NOT REMOVE OR MODIFY THE FOLLOWING COMMENTED LINES!
'MemberInfo=12,0,0,100
Public Property Get Max() As Single
    Max = m_Max
End Property

Public Property Let Max(ByVal New_Max As Single)
    m_Max = New_Max
    PropertyChanged "Max"
End Property

'WARNING! DO NOT REMOVE OR MODIFY THE FOLLOWING COMMENTED LINES!
'MemberInfo=12,0,0,50
Public Property Get Value() As Single
    Value = m_Value
End Property

'WARNING! DO NOT REMOVE OR MODIFY THE FOLLOWING COMMENTED LINES!
'MemberInfo=13,0,0,%
Public Property Get Units() As String
    Units = m_Units
End Property

Public Property Let Units(ByVal New_Units As String)
    m_Units = New_Units
    PropertyChanged "Units"

    Label1.Caption = m_Caption & " " & Fix(m_Value) & " " & m_Units
End Property

'WARNING! DO NOT REMOVE OR MODIFY THE FOLLOWING COMMENTED LINES!
'MemberInfo=13,0,0,
Public Property Get Caption() As String
    Caption = m_Caption
End Property

Public Property Let Caption(ByVal New_Caption As String)
    m_Caption = New_Caption
    PropertyChanged "Caption"

    Label1.Caption = m_Caption & " " & Fix(m_Value) & " " & m_Units
End Property

'Initialize Properties for User Control
Private Sub UserControl_InitProperties()
    m_Min = m_def_Min
    m_Max = m_def_Max
    m_Value = m_def_Value
    m_Units = m_def_Units
    m_Caption = m_def_Caption
End Sub

'Load property values from storage
Private Sub UserControl_ReadProperties(PropBag As PropertyBag)

    m_Min = PropBag.ReadProperty("Min", m_def_Min)
    m_Max = PropBag.ReadProperty("Max", m_def_Max)
    m_Value = PropBag.ReadProperty("Value", m_def_Value)
    m_Units = PropBag.ReadProperty("Units", m_def_Units)
    m_Caption = PropBag.ReadProperty("Caption", m_def_Caption)
    
    Label1.Caption = m_Caption & " " & Fix(m_Value) & " " & m_Units
End Sub

'Write property values to storage
Private Sub UserControl_WriteProperties(PropBag As PropertyBag)

    Call PropBag.WriteProperty("Min", m_Min, m_def_Min)
    Call PropBag.WriteProperty("Max", m_Max, m_def_Max)
    Call PropBag.WriteProperty("Value", m_Value, m_def_Value)
    Call PropBag.WriteProperty("Units", m_Units, m_def_Units)
    Call PropBag.WriteProperty("Caption", m_Caption, m_def_Caption)
End Sub

