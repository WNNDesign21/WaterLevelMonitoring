class SensorDataModel {
  final double distance;
  final int validCount;
  final DateTime? createdAt;
  final double? waterLevel;
  
  // Calibration fields often included in sensor response
  final double? elevationMdpl;
  final double? sensorToBank;
  final double? riverDepth;

  SensorDataModel({
    required this.distance,
    required this.validCount,
    this.createdAt,
    this.waterLevel,
    this.elevationMdpl,
    this.sensorToBank,
    this.riverDepth,
  });

  factory SensorDataModel.fromJson(Map<String, dynamic> response) {
    final data = response['data'] ?? {};
    final config = response['config'] ?? {};
    
    return SensorDataModel(
      distance: double.tryParse(data['distance']?.toString() ?? '0') ?? 0.0,
      validCount: int.tryParse(data['valid_count']?.toString() ?? '0') ?? 0,
      createdAt: data['created_at'] != null ? DateTime.parse(data['created_at']) : null,
      waterLevel: double.tryParse(data['water_level']?.toString() ?? '0'),
      elevationMdpl: double.tryParse(config['elevation_mdpl']?.toString() ?? ''),
      sensorToBank: double.tryParse(config['sensor_to_bank']?.toString() ?? ''),
      riverDepth: double.tryParse(config['river_depth']?.toString() ?? ''),
    );
  }
}
