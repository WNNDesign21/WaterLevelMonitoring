class DeviceModel {
  final int? id;
  final String slug;
  final String name;
  final String? location;
  final double? latitude;
  final double? longitude;
  final double? waterLevel;
  final String? siagaStatus;
  final String? updatedAt;

  DeviceModel({
    this.id,
    required this.slug,
    required this.name,
    this.location,
    this.latitude,
    this.longitude,
    this.waterLevel,
    this.siagaStatus,
    this.updatedAt,
  });

  factory DeviceModel.fromJson(Map<String, dynamic> json) {
    return DeviceModel(
      id: json['id'],
      slug: json['slug'] ?? '',
      name: json['name'] ?? '',
      location: json['location'],
      latitude: double.tryParse(json['latitude']?.toString() ?? ''),
      longitude: double.tryParse(json['longitude']?.toString() ?? ''),
      waterLevel: double.tryParse(json['water_level']?.toString() ?? ''),
      siagaStatus: json['siaga_status'],
      updatedAt: json['updated_at'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'slug': slug,
      'name': name,
      'location': location,
      'latitude': latitude,
      'longitude': longitude,
      'water_level': waterLevel,
      'siaga_status': siagaStatus,
      'updated_at': updatedAt,
    };
  }
}
