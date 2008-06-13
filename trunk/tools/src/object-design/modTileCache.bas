Attribute VB_Name = "modTileCache"
Option Explicit

Public TileCache() As Collection
Public TileMaxY() As Integer
Public TileLocation() As Collection
Public CacheInitialized As Boolean

Public Sub InitCache(Entries As Integer)
    On Error Resume Next
    If CacheInitialized Then Exit Sub
    ReDim TileCache(Entries - 1)
    ReDim TileMaxY(Entries - 1)
    ReDim TileLocation(Entries - 1)
    CacheInitialized = True
End Sub

