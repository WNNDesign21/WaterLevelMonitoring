class UserModel {
  final int? id;
  final String name;
  final String email;
  final String? phone;
  final String? address;
  final String? photoUrl;
  final String? emergencyPhone;
  final double? latitude;
  final double? longitude;

  UserModel({
    this.id,
    required this.name,
    required this.email,
    this.phone,
    this.address,
    this.photoUrl,
    this.emergencyPhone,
    this.latitude,
    this.longitude,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'],
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'],
      address: json['address'],
      photoUrl: json['avatar'] ?? json['photo_url'],
      emergencyPhone: json['emergency_phone'],
      latitude: double.tryParse(json['latitude']?.toString() ?? ''),
      longitude: double.tryParse(json['longitude']?.toString() ?? ''),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
      'address': address,
      'avatar': photoUrl,
      'emergency_phone': emergencyPhone,
      'latitude': latitude,
      'longitude': longitude,
    };
  }
}
