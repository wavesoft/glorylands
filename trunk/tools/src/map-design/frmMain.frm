VERSION 5.00
Object = "{831FDD16-0C5C-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCTL.OCX"
Object = "{86CF1D34-0C5F-11D2-A9FC-0000F8754DA1}#2.0#0"; "MSCOMCT2.OCX"
Begin VB.MDIForm frmMain 
   BackColor       =   &H8000000C&
   Caption         =   "Wavesoft GloryLands Level Editor"
   ClientHeight    =   8625
   ClientLeft      =   165
   ClientTop       =   450
   ClientWidth     =   13110
   Icon            =   "frmMain.frx":0000
   LinkTopic       =   "MDIForm1"
   NegotiateToolbars=   0   'False
   StartUpPosition =   2  'CenterScreen
   Begin VB.Timer Timer2 
      Interval        =   100
      Left            =   5880
      Top             =   3480
   End
   Begin VB.PictureBox Picture1 
      Align           =   4  'Align Right
      BackColor       =   &H00FFFFFF&
      BorderStyle     =   0  'None
      Height          =   7260
      Left            =   8910
      ScaleHeight     =   7260
      ScaleWidth      =   4200
      TabIndex        =   3
      Top             =   570
      Width           =   4200
      Begin VB.PictureBox Picture4 
         BackColor       =   &H00FFFFFF&
         BorderStyle     =   0  'None
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   8.25
            Charset         =   161
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   5295
         Left            =   0
         ScaleHeight     =   5295
         ScaleWidth      =   4215
         TabIndex        =   15
         Top             =   360
         Visible         =   0   'False
         Width           =   4215
         Begin VB.CheckBox chkZOver 
            BackColor       =   &H00FFFFFF&
            Caption         =   "Keep grid above objects"
            BeginProperty Font 
               Name            =   "Tahoma"
               Size            =   8.25
               Charset         =   0
               Weight          =   400
               Underline       =   0   'False
               Italic          =   0   'False
               Strikethrough   =   0   'False
            EndProperty
            Height          =   255
            Left            =   120
            TabIndex        =   20
            Top             =   4560
            Width           =   3975
         End
         Begin VB.Timer Timer1 
            Interval        =   10
            Left            =   3720
            Top             =   960
         End
         Begin MSComCtl2.FlatScrollBar scrlAtt 
            Height          =   4215
            Left            =   120
            TabIndex        =   16
            Top             =   240
            Width           =   1815
            _ExtentX        =   3201
            _ExtentY        =   7435
            _Version        =   393216
            LargeChange     =   10
            Max             =   100
            Orientation     =   1179648
            Value           =   100
         End
         Begin VB.Shape sProg 
            BorderColor     =   &H00808080&
            FillColor       =   &H00C0C0C0&
            FillStyle       =   5  'Downward Diagonal
            Height          =   135
            Index           =   11
            Left            =   2040
            Top             =   3600
            Width           =   255
         End
         Begin VB.Shape sProg 
            BorderColor     =   &H00808080&
            FillColor       =   &H00C0C0C0&
            FillStyle       =   5  'Downward Diagonal
            Height          =   135
            Index           =   10
            Left            =   2040
            Top             =   3360
            Width           =   375
         End
         Begin VB.Shape sProg 
            BorderColor     =   &H00808080&
            FillColor       =   &H00C0C0C0&
            FillStyle       =   5  'Downward Diagonal
            Height          =   135
            Index           =   9
            Left            =   2040
            Top             =   3120
            Width           =   495
         End
         Begin VB.Shape sProg 
            BorderColor     =   &H00808080&
            FillColor       =   &H00C0C0C0&
            FillStyle       =   5  'Downward Diagonal
            Height          =   135
            Index           =   8
            Left            =   2040
            Top             =   2880
            Width           =   615
         End
         Begin VB.Shape sProg 
            BorderColor     =   &H00808080&
            FillColor       =   &H00C0C0C0&
            FillStyle       =   5  'Downward Diagonal
            Height          =   135
            Index           =   7
            Left            =   2040
            Top             =   2640
            Width           =   735
         End
         Begin VB.Shape sProg 
            BorderColor     =   &H00808080&
            FillColor       =   &H00C0C0C0&
            FillStyle       =   5  'Downward Diagonal
            Height          =   135
            Index           =   6
            Left            =   2040
            Top             =   2400
            Width           =   855
         End
         Begin VB.Shape sProg 
            BorderColor     =   &H00808080&
            FillColor       =   &H00C0C0C0&
            FillStyle       =   5  'Downward Diagonal
            Height          =   135
            Index           =   5
            Left            =   2040
            Top             =   2160
            Width           =   975
         End
         Begin VB.Shape sProg 
            BorderColor     =   &H00808080&
            FillColor       =   &H00C0C0C0&
            FillStyle       =   5  'Downward Diagonal
            Height          =   135
            Index           =   4
            Left            =   2040
            Top             =   1920
            Width           =   1095
         End
         Begin VB.Shape sProg 
            BorderColor     =   &H00808080&
            FillColor       =   &H00C0C0C0&
            FillStyle       =   5  'Downward Diagonal
            Height          =   135
            Index           =   3
            Left            =   2040
            Top             =   1680
            Width           =   1215
         End
         Begin VB.Shape sProg 
            BorderColor     =   &H00808080&
            FillColor       =   &H00C0C0C0&
            FillStyle       =   5  'Downward Diagonal
            Height          =   135
            Index           =   2
            Left            =   2040
            Top             =   1440
            Width           =   1335
         End
         Begin VB.Shape sProg 
            BorderColor     =   &H00808080&
            FillColor       =   &H00C0C0C0&
            FillStyle       =   5  'Downward Diagonal
            Height          =   135
            Index           =   1
            Left            =   2040
            Top             =   1200
            Width           =   1455
         End
         Begin VB.Shape sProg 
            BorderColor     =   &H00808080&
            FillColor       =   &H00C0C0C0&
            FillStyle       =   5  'Downward Diagonal
            Height          =   135
            Index           =   0
            Left            =   2040
            Top             =   960
            Width           =   1575
         End
         Begin VB.Label Label2 
            Alignment       =   2  'Center
            BackStyle       =   0  'Transparent
            Caption         =   "Do not attenuate"
            BeginProperty Font 
               Name            =   "Tahoma"
               Size            =   8.25
               Charset         =   0
               Weight          =   700
               Underline       =   0   'False
               Italic          =   0   'False
               Strikethrough   =   0   'False
            EndProperty
            ForeColor       =   &H00008000&
            Height          =   255
            Left            =   2040
            MouseIcon       =   "frmMain.frx":B84A
            MousePointer    =   99  'Custom
            TabIndex        =   18
            Top             =   3960
            Width           =   1575
         End
         Begin VB.Label Label1 
            Alignment       =   2  'Center
            BackStyle       =   0  'Transparent
            Caption         =   "Unable to enter"
            BeginProperty Font 
               Name            =   "Tahoma"
               Size            =   8.25
               Charset         =   0
               Weight          =   700
               Underline       =   0   'False
               Italic          =   0   'False
               Strikethrough   =   0   'False
            EndProperty
            ForeColor       =   &H000000C0&
            Height          =   255
            Left            =   2040
            MouseIcon       =   "frmMain.frx":B99C
            MousePointer    =   99  'Custom
            TabIndex        =   17
            Top             =   480
            Width           =   1575
         End
         Begin VB.Line Line2 
            BorderColor     =   &H00808080&
            X1              =   2040
            X2              =   3600
            Y1              =   3840
            Y2              =   3840
         End
         Begin VB.Line Line1 
            BorderColor     =   &H00808080&
            X1              =   2040
            X2              =   3600
            Y1              =   840
            Y2              =   840
         End
         Begin VB.Label Label3 
            Alignment       =   1  'Right Justify
            BackColor       =   &H00000000&
            BeginProperty Font 
               Name            =   "Tahoma"
               Size            =   8.25
               Charset         =   161
               Weight          =   700
               Underline       =   0   'False
               Italic          =   0   'False
               Strikethrough   =   0   'False
            EndProperty
            ForeColor       =   &H00FFFFFF&
            Height          =   255
            Left            =   0
            TabIndex        =   19
            Top             =   480
            Width           =   4215
         End
      End
      Begin VB.PictureBox Picture3 
         BackColor       =   &H00404040&
         BorderStyle     =   0  'None
         Height          =   1455
         Left            =   0
         ScaleHeight     =   1455
         ScaleWidth      =   4215
         TabIndex        =   4
         Top             =   360
         Width           =   4215
         Begin MSComCtl2.FlatScrollBar vScroll1 
            Height          =   1455
            Left            =   3900
            TabIndex        =   12
            Top             =   0
            Width           =   255
            _ExtentX        =   450
            _ExtentY        =   2566
            _Version        =   393216
            Enabled         =   0   'False
            Max             =   1
            Orientation     =   1179648
         End
         Begin VB.Label lblList 
            BackColor       =   &H00404040&
            Caption         =   " Ground Objects"
            BeginProperty Font 
               Name            =   "Tahoma"
               Size            =   8.25
               Charset         =   161
               Weight          =   400
               Underline       =   0   'False
               Italic          =   0   'False
               Strikethrough   =   0   'False
            EndProperty
            ForeColor       =   &H8000000E&
            Height          =   225
            Index           =   1
            Left            =   0
            MouseIcon       =   "frmMain.frx":BAEE
            MousePointer    =   99  'Custom
            TabIndex        =   9
            Top             =   240
            Width           =   3855
         End
         Begin VB.Label lblList 
            BackColor       =   &H00404040&
            Caption         =   " Ground Objects"
            BeginProperty Font 
               Name            =   "Tahoma"
               Size            =   8.25
               Charset         =   161
               Weight          =   400
               Underline       =   0   'False
               Italic          =   0   'False
               Strikethrough   =   0   'False
            EndProperty
            ForeColor       =   &H8000000E&
            Height          =   225
            Index           =   2
            Left            =   0
            MouseIcon       =   "frmMain.frx":BC40
            MousePointer    =   99  'Custom
            TabIndex        =   8
            Top             =   480
            Width           =   3855
         End
         Begin VB.Label lblList 
            BackColor       =   &H00404040&
            Caption         =   " Ground Objects"
            BeginProperty Font 
               Name            =   "Tahoma"
               Size            =   8.25
               Charset         =   161
               Weight          =   400
               Underline       =   0   'False
               Italic          =   0   'False
               Strikethrough   =   0   'False
            EndProperty
            ForeColor       =   &H8000000E&
            Height          =   225
            Index           =   3
            Left            =   0
            MouseIcon       =   "frmMain.frx":BD92
            MousePointer    =   99  'Custom
            TabIndex        =   7
            Top             =   720
            Width           =   3855
         End
         Begin VB.Label lblList 
            BackColor       =   &H00404040&
            Caption         =   " Ground Objects"
            BeginProperty Font 
               Name            =   "Tahoma"
               Size            =   8.25
               Charset         =   161
               Weight          =   400
               Underline       =   0   'False
               Italic          =   0   'False
               Strikethrough   =   0   'False
            EndProperty
            ForeColor       =   &H8000000E&
            Height          =   225
            Index           =   4
            Left            =   0
            MouseIcon       =   "frmMain.frx":BEE4
            MousePointer    =   99  'Custom
            TabIndex        =   6
            Top             =   960
            Width           =   3855
         End
         Begin VB.Label lblList 
            BackColor       =   &H00404040&
            Caption         =   " Ground Objects"
            BeginProperty Font 
               Name            =   "Tahoma"
               Size            =   8.25
               Charset         =   161
               Weight          =   400
               Underline       =   0   'False
               Italic          =   0   'False
               Strikethrough   =   0   'False
            EndProperty
            ForeColor       =   &H8000000E&
            Height          =   225
            Index           =   5
            Left            =   0
            MouseIcon       =   "frmMain.frx":C036
            MousePointer    =   99  'Custom
            TabIndex        =   5
            Top             =   1200
            Width           =   3855
         End
         Begin VB.Label lblList 
            BackColor       =   &H00404040&
            Caption         =   " Ground Objects"
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
            Height          =   225
            Index           =   0
            Left            =   0
            MouseIcon       =   "frmMain.frx":C188
            MousePointer    =   99  'Custom
            TabIndex        =   10
            Top             =   0
            Width           =   3855
         End
      End
      Begin MSComctlLib.ImageList ImageList1 
         Left            =   600
         Top             =   2640
         _ExtentX        =   1005
         _ExtentY        =   1005
         BackColor       =   -2147483643
         ImageWidth      =   64
         ImageHeight     =   64
         MaskColor       =   12632256
         _Version        =   393216
         BeginProperty Images {2C247F25-8591-11D1-B16A-00C0F0283628} 
            NumListImages   =   1
            BeginProperty ListImage1 {2C247F27-8591-11D1-B16A-00C0F0283628} 
               Picture         =   "frmMain.frx":C2DA
               Key             =   ""
            EndProperty
         EndProperty
      End
      Begin MSComctlLib.ListView ListView1 
         Height          =   5655
         Left            =   60
         TabIndex        =   11
         Top             =   1920
         Width           =   4095
         _ExtentX        =   7223
         _ExtentY        =   9975
         LabelWrap       =   -1  'True
         HideSelection   =   -1  'True
         _Version        =   393217
         Icons           =   "ImageList1"
         ForeColor       =   -2147483640
         BackColor       =   -2147483643
         Appearance      =   0
         NumItems        =   0
      End
      Begin MSComctlLib.TabStrip TabStrip1 
         Height          =   375
         Left            =   0
         TabIndex        =   14
         Top             =   0
         Width           =   4215
         _ExtentX        =   7435
         _ExtentY        =   661
         _Version        =   393216
         BeginProperty Tabs {1EFB6598-857C-11D1-B16A-00C0F0283628} 
            NumTabs         =   2
            BeginProperty Tab1 {1EFB659A-857C-11D1-B16A-00C0F0283628} 
               Caption         =   "Gameobjects"
               ImageVarType    =   2
            EndProperty
            BeginProperty Tab2 {1EFB659A-857C-11D1-B16A-00C0F0283628} 
               Caption         =   "Attennuation Grid"
               ImageVarType    =   2
            EndProperty
         EndProperty
         BeginProperty Font {0BE35203-8F91-11CE-9DE3-00AA004BB851} 
            Name            =   "Tahoma"
            Size            =   8.25
            Charset         =   161
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
      End
   End
   Begin MSComctlLib.ImageList ImageList2 
      Left            =   5280
      Top             =   3480
      _ExtentX        =   1005
      _ExtentY        =   1005
      BackColor       =   -2147483643
      ImageWidth      =   16
      ImageHeight     =   16
      MaskColor       =   12632256
      _Version        =   393216
      BeginProperty Images {2C247F25-8591-11D1-B16A-00C0F0283628} 
         NumListImages   =   9
         BeginProperty ListImage1 {2C247F27-8591-11D1-B16A-00C0F0283628} 
            Picture         =   "frmMain.frx":F32C
            Key             =   ""
         EndProperty
         BeginProperty ListImage2 {2C247F27-8591-11D1-B16A-00C0F0283628} 
            Picture         =   "frmMain.frx":F8C6
            Key             =   ""
         EndProperty
         BeginProperty ListImage3 {2C247F27-8591-11D1-B16A-00C0F0283628} 
            Picture         =   "frmMain.frx":FE60
            Key             =   ""
         EndProperty
         BeginProperty ListImage4 {2C247F27-8591-11D1-B16A-00C0F0283628} 
            Picture         =   "frmMain.frx":103FA
            Key             =   ""
         EndProperty
         BeginProperty ListImage5 {2C247F27-8591-11D1-B16A-00C0F0283628} 
            Picture         =   "frmMain.frx":10994
            Key             =   ""
         EndProperty
         BeginProperty ListImage6 {2C247F27-8591-11D1-B16A-00C0F0283628} 
            Picture         =   "frmMain.frx":10F2E
            Key             =   ""
         EndProperty
         BeginProperty ListImage7 {2C247F27-8591-11D1-B16A-00C0F0283628} 
            Picture         =   "frmMain.frx":114C8
            Key             =   ""
         EndProperty
         BeginProperty ListImage8 {2C247F27-8591-11D1-B16A-00C0F0283628} 
            Picture         =   "frmMain.frx":11A62
            Key             =   ""
         EndProperty
         BeginProperty ListImage9 {2C247F27-8591-11D1-B16A-00C0F0283628} 
            Picture         =   "frmMain.frx":11FFC
            Key             =   ""
         EndProperty
      EndProperty
   End
   Begin MSComctlLib.Toolbar Toolbar1 
      Align           =   1  'Align Top
      Height          =   570
      Left            =   0
      TabIndex        =   2
      Top             =   0
      Width           =   13110
      _ExtentX        =   23125
      _ExtentY        =   1005
      ButtonWidth     =   1508
      ButtonHeight    =   953
      Appearance      =   1
      Style           =   1
      ImageList       =   "ImageList2"
      _Version        =   393216
      BeginProperty Buttons {66833FE8-8583-11D1-B16A-00C0F0283628} 
         NumButtons      =   10
         BeginProperty Button1 {66833FEA-8583-11D1-B16A-00C0F0283628} 
            Caption         =   "New"
            Key             =   "NEW"
            ImageIndex      =   1
         EndProperty
         BeginProperty Button2 {66833FEA-8583-11D1-B16A-00C0F0283628} 
            Style           =   3
         EndProperty
         BeginProperty Button3 {66833FEA-8583-11D1-B16A-00C0F0283628} 
            Caption         =   "Open"
            Key             =   "OPEN"
            ImageIndex      =   2
         EndProperty
         BeginProperty Button4 {66833FEA-8583-11D1-B16A-00C0F0283628} 
            Caption         =   "Save"
            Key             =   "SAVE"
            ImageIndex      =   3
         EndProperty
         BeginProperty Button5 {66833FEA-8583-11D1-B16A-00C0F0283628} 
            Caption         =   "Save As"
            Key             =   "SAVEAS"
            ImageIndex      =   4
         EndProperty
         BeginProperty Button6 {66833FEA-8583-11D1-B16A-00C0F0283628} 
            Style           =   3
         EndProperty
         BeginProperty Button7 {66833FEA-8583-11D1-B16A-00C0F0283628} 
            Caption         =   "Compile"
            Key             =   "COMPILE"
            ImageIndex      =   5
            Style           =   5
            BeginProperty ButtonMenus {66833FEC-8583-11D1-B16A-00C0F0283628} 
               NumButtonMenus  =   7
               BeginProperty ButtonMenu1 {66833FEE-8583-11D1-B16A-00C0F0283628} 
                  Key             =   "C_PHPS"
                  Text            =   "PHP Script generated map (PHP)"
               EndProperty
               BeginProperty ButtonMenu2 {66833FEE-8583-11D1-B16A-00C0F0283628} 
                  Key             =   "C_SERIALIZED"
                  Text            =   "Serialized PHP Array Format (CMP)"
               EndProperty
               BeginProperty ButtonMenu3 {66833FEE-8583-11D1-B16A-00C0F0283628} 
                  Key             =   "C_ASCII"
                  Text            =   "ASCII Packed Map (AMP)"
               EndProperty
               BeginProperty ButtonMenu4 {66833FEE-8583-11D1-B16A-00C0F0283628} 
                  Key             =   "C_CHUNK"
                  Text            =   "Chunk-Model Serialized map (CMMP)"
               EndProperty
               BeginProperty ButtonMenu5 {66833FEE-8583-11D1-B16A-00C0F0283628} 
                  Key             =   "C_SPLIT"
                  Text            =   "Javascript/PHP Separated Chunk mode (JMAP, ZMAP)"
               EndProperty
               BeginProperty ButtonMenu6 {66833FEE-8583-11D1-B16A-00C0F0283628} 
                  Text            =   "-"
               EndProperty
               BeginProperty ButtonMenu7 {66833FEE-8583-11D1-B16A-00C0F0283628} 
                  Key             =   "C_REPEAT"
                  Text            =   "Repeat last compilation (CTRL+C)"
               EndProperty
            EndProperty
         EndProperty
         BeginProperty Button8 {66833FEA-8583-11D1-B16A-00C0F0283628} 
            Caption         =   "Map Info"
            Key             =   "INFO"
            ImageIndex      =   7
         EndProperty
         BeginProperty Button9 {66833FEA-8583-11D1-B16A-00C0F0283628} 
            Style           =   3
         EndProperty
         BeginProperty Button10 {66833FEA-8583-11D1-B16A-00C0F0283628} 
            Caption         =   "Reference"
            Key             =   "HELP"
            ImageIndex      =   9
         EndProperty
      EndProperty
   End
   Begin VB.PictureBox Picture2 
      Align           =   2  'Align Bottom
      Height          =   495
      Left            =   0
      ScaleHeight     =   435
      ScaleWidth      =   13050
      TabIndex        =   0
      Top             =   8130
      Visible         =   0   'False
      Width           =   13110
      Begin VB.PictureBox picResizePrev 
         AutoRedraw      =   -1  'True
         BackColor       =   &H00FFFFFF&
         BorderStyle     =   0  'None
         Height          =   375
         Left            =   1080
         ScaleHeight     =   375
         ScaleWidth      =   735
         TabIndex        =   1
         Top             =   120
         Width           =   735
      End
      Begin VB.Image imgPrevPic 
         Height          =   255
         Left            =   0
         Top             =   0
         Width           =   255
      End
   End
   Begin MSComctlLib.StatusBar StatusBar1 
      Align           =   2  'Align Bottom
      Height          =   300
      Left            =   0
      TabIndex        =   13
      Top             =   7830
      Width           =   13110
      _ExtentX        =   23125
      _ExtentY        =   529
      _Version        =   393216
      BeginProperty Panels {8E3867A5-8586-11D1-B16A-00C0F0283628} 
         NumPanels       =   1
         BeginProperty Panel1 {8E3867AB-8586-11D1-B16A-00C0F0283628} 
            AutoSize        =   1
            Object.Width           =   22622
         EndProperty
      EndProperty
   End
End
Attribute VB_Name = "frmMain"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
Dim lSel As Integer
Dim selID As Integer
Dim GroupCount As Integer
Dim Groups() As String
Public AttValue As Integer

Private Sub LoadCategory(ByVal Id As Integer)
    On Error Resume Next
    Dim s As String, i
    ListView1.ListItems.Clear
    s = Dir(FixPath(App.Path) & "\objects\" & Groups(Id) & "-*.cob")
    Do While s <> ""
        i = StorePreviewOf(s)
        ListView1.ListItems.Add , , s, i
        s = Dir
    Loop
End Sub

Private Sub SelectActiveListItem()
    On Error Resume Next
    Dim i
    For i = 0 To lblList.UBound
        lblList(i).FontBold = (i + vScroll1.Value = selID)
    Next i
End Sub

Private Sub LoadGroups()
    On Error Resume Next
    Dim s As String, f() As String
    Dim i
    s = Dir(FixPath(App.Path) & "\objects\*.cob")
    Do While s <> ""
        If s <> "." And s <> ".." Then
            f = Split(s, "-")
            f(0) = StrConv(Trim(f(0)), vbProperCase)
            For i = 0 To GroupCount - 1
                If Groups(i) = f(0) Then GoTo ResDo
            Next i
            ReDim Preserve Groups(GroupCount)
            Groups(GroupCount) = f(0)
            GroupCount = GroupCount + 1
        End If
ResDo:
        s = Dir
    Loop
    vScroll1.Value = 0
    If GroupCount < lblList.Count Then
        vScroll1.Enabled = False
    Else
        vScroll1.Enabled = True
        vScroll1.Max = GroupCount - lblList.Count
    End If
    ShowListPage
End Sub

Private Sub ShowListPage()
    Dim i
    UnSelList
    For i = vScroll1.Value To vScroll1.Value + lblList.Count - 1
        If i < GroupCount Then
            lblList(i - vScroll1.Value).Visible = True
            lblList(i - vScroll1.Value).Caption = " " & Groups(i)
        Else
            lblList(i - vScroll1.Value).Visible = False
        End If
    Next i
    SelectActiveListItem
End Sub

Public Sub UnSelList()
    If lSel < 0 Then Exit Sub
    lblList(lSel).BackColor = &H404040
    lblList(lSel).ForeColor = &HFFFFFF
    lSel = -1
End Sub

Private Sub Label1_Click()
    scrlAtt.Value = 0
End Sub

Private Sub Label2_Click()
    scrlAtt.Value = 100
End Sub

Private Sub lblList_Click(Index As Integer)
    Dim i
    For i = 0 To lblList.UBound
        lblList(i).FontBold = (Index = i)
    Next i
    selID = Index + vScroll1.Value
    LoadCategory vScroll1.Value + Index
End Sub

Private Sub lblList_MouseMove(Index As Integer, Button As Integer, Shift As Integer, X As Single, Y As Single)
    If lSel <> Index Then
        UnSelList
        lSel = Index
        lblList(lSel).BackColor = &HFFFFFF
        lblList(lSel).ForeColor = &H0
    End If
End Sub

Private Sub ListView1_Click()
    If Me.ActiveForm Is Nothing Then Exit Sub
    Me.ActiveForm.SetActiveObject ListView1.SelectedItem.Text
    Me.ActiveForm.SetFocus
End Sub

Private Sub ListView1_MouseMove(Button As Integer, Shift As Integer, X As Single, Y As Single)
    UnSelList
End Sub

Private Sub MDIForm_Load()
    ' Init preview size
    picResizePrev.Width = 64 * Screen.TwipsPerPixelX
    picResizePrev.Height = 64 * Screen.TwipsPerPixelX
    Me.Show
    
    LoadGroups
    
    'Dim A As New clsPHPArray
    'A(2) = "first"
    'A(1) = "second"
    'A(3) = "third"
    'A(0)(0) = "fourth"
    'A(0)(1) = "Good"
    'MsgBox A.StructPrint
    'A.SortByKey
    'MsgBox A.StructPrint
    
    'Dim l As Long
    'l = FreeFile
    'Open "C:\out.array" For Output As #l
    'Close #l
    'Open "C:\out.array" For Binary As #l
    'A.SaveToFile l
    'A.LoadFromFile l
    'Close #l
    
    'MsgBox A.StructPrint
    'End
End Sub

Private Function StorePreviewOf(ByVal FileName As String) As Integer
    ' Load preview into image (to get it's size)
    Set imgPrevPic.Picture = GetObjPreview(FixPath(App.Path) & "objects\" & FileName)
    If imgPrevPic.Picture = 0 Then
        StorePreviewOf = 1
        Exit Function
    End If
    
    ' Check if exists
    Dim i
    For i = 1 To ImageList1.ListImages.Count
        If LCase(Trim(ImageList1.ListImages(i).Key)) = LCase(Trim(FileName)) Then
            StorePreviewOf = i
            Exit Function
        End If
    Next i
    
    ' Calculate the new size
    Dim aspectRatio As Single
    Dim nW, nH, pX, pY
    If (imgPrevPic.Width >= picResizePrev.Width) Or (imgPrevPic.Height >= picResizePrev.Height) Then
        If (imgPrevPic.Width > imgPrevPic.Height) Then
            aspectRatio = imgPrevPic.Height / imgPrevPic.Width
            nW = picResizePrev.Width
            nH = nW * aspectRatio
            pX = 0
            pY = (picResizePrev.ScaleHeight - nH) / 2
        Else
            aspectRatio = imgPrevPic.Width / imgPrevPic.Height
            nH = picResizePrev.Height
            nW = nH * aspectRatio
            pY = 0
            pX = (picResizePrev.ScaleWidth - nW) / 2
        End If
    Else
        nH = imgPrevPic.Height
        nW = imgPrevPic.Width
        pX = (picResizePrev.ScaleWidth - nW) / 2
        pY = (picResizePrev.ScaleHeight - nH) / 2
    End If
    
    ' Blit into picture box with the new size
    picResizePrev.Cls
    picResizePrev.PaintPicture imgPrevPic.Picture, pX, pY, nW, nH
    
    ' Store into image list
    ImageList1.ListImages.Add , FileName, picResizePrev.Image
    
    ' Return the handle
    StorePreviewOf = ImageList1.ListImages.Count
End Function

Private Sub MDIForm_MouseMove(Button As Integer, Shift As Integer, X As Single, Y As Single)
    UnSelList
End Sub

Private Sub MDIForm_Resize()
    On Error Resume Next
    ListView1.Height = Me.Height - ListView1.Top - Picture1.Top - 750
End Sub

Private Sub Picture1_Click()
    Picture1.Align = vbAlignLeft
End Sub

Private Sub Picture1_MouseMove(Button As Integer, Shift As Integer, X As Single, Y As Single)
    UnSelList
End Sub

Private Sub Picture3_MouseMove(Button As Integer, Shift As Integer, X As Single, Y As Single)
    UnSelList
End Sub

Private Sub scrlAtt_Change()
    AttValue = scrlAtt.Value
    Timer1.Enabled = True
End Sub

Private Sub scrlAtt_Scroll()
    AttValue = scrlAtt.Value
    Timer1.Enabled = True
End Sub

Private Sub TabStrip1_Click()
    If TabStrip1.SelectedItem.Index = 1 Then
        Picture4.Visible = False
        ListView1.Visible = True
        Picture3.Visible = True
    Else
        Picture4.Visible = True
        ListView1.Visible = False
        Picture3.Visible = False
    End If
End Sub

Private Sub Timer1_Timer()
    Dim rP As Integer, rDiff As Integer
    rP = (scrlAtt.Top + 255) + (scrlAtt.Height - 510 - 256) * AttValue / 100
    rDiff = Label3.Top - rP
    Label3.Top = Label3.Top - rDiff / 5
    
    If Abs(rDiff) <= 2 Then
        Timer1.Enabled = False
    End If
    
    Dim iValue As Integer
    iValue = 100 - AttValue
    If iValue = 100 Then
        Label3.BackColor = vbRed
        Label3.Caption = ""
    ElseIf iValue = 0 Then
        Label3.BackColor = vbGreen
        Label3.Caption = ""
    Else
        Label3.BackColor = vbBlack
        Label3.Caption = iValue & " %   "
    End If
End Sub

Private Sub Timer2_Timer()
    If Me.ActiveForm Is Nothing Then Exit Sub
    Me.ActiveForm.SetAttDrawing (TabStrip1.SelectedItem.Index = 2)
    Me.ActiveForm.SetAttOvergrid (chkZOver.Value = 1)
End Sub

Private Sub Toolbar1_ButtonClick(ByVal Button As MSComctlLib.Button)
    Dim f As frmMap
    If Button.Key = "NEW" Then
        Set f = New frmMap
        Load f
    ElseIf Button.Key = "SAVE" Then
        If Not Me.ActiveForm Is Nothing Then Me.ActiveForm.Save
    ElseIf Button.Key = "SAVEAS" Then
        If Not Me.ActiveForm Is Nothing Then Me.ActiveForm.SaveAs
    ElseIf Button.Key = "OPEN" Then
        If Not Me.ActiveForm Is Nothing Then
            Me.ActiveForm.LoadFile
        Else
            Set f = New frmMap
            Load f
            f.LoadFile
        End If
    ElseIf Button.Key = "COMPILE" Then
        If Not Me.ActiveForm Is Nothing Then
            Me.ActiveForm.Compile cmSplit
        End If
    ElseIf Button.Key = "INFO" Then
        If Not Me.ActiveForm Is Nothing Then
            frmMapInfo.Display Me.ActiveForm
        End If
    ElseIf Button.Key = "HELP" Then
        frmHelp.Show
    End If
End Sub

Private Sub Toolbar1_ButtonMenuClick(ByVal ButtonMenu As MSComctlLib.ButtonMenu)
    If Me.ActiveForm Is Nothing Then Exit Sub
    If ButtonMenu.Key = "C_PHPS" Then
        Me.ActiveForm.Compile cmPHP
    ElseIf ButtonMenu.Key = "C_SERIALIZED" Then
        Me.ActiveForm.Compile cmSerialized
    ElseIf ButtonMenu.Key = "C_CHUNK" Then
        Me.ActiveForm.Compile cmChunk
    ElseIf ButtonMenu.Key = "C_REPEAT" Then
        Me.ActiveForm.Compile smRepeat
    ElseIf ButtonMenu.Key = "C_SPLIT" Then
        Me.ActiveForm.Compile cmSplit
    End If
End Sub

Private Sub Toolbar1_MouseMove(Button As Integer, Shift As Integer, X As Single, Y As Single)
    UnSelList
End Sub

Private Sub vScroll1_Change()
    ShowListPage
End Sub

Private Sub vScroll1_Scroll()
    ShowListPage
End Sub
