import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../core/api_client.dart';
import '../services/api_service.dart';

class ReportTab extends StatefulWidget {
  const ReportTab({super.key});

  @override
  State<ReportTab> createState() => _ReportTabState();
}

class _ReportTabState extends State<ReportTab> {
  final ApiService _apiService = ApiService(ApiClient());

  // Real data from API
  List<Map<String, dynamic>>? vehicleData;
  List<Map<String, dynamic>>? costData;
  List<Map<String, dynamic>>? driverData;
  
  // Loading states
  bool isLoadingVehicle = false;
  bool isLoadingCost = false;
  bool isLoadingDriver = false;

  // Pagination state
  int vehicleShown = 10;
  int costShown = 10;
  int driverShown = 10;

  // Date filter state
  DateTime? vehicleFrom, vehicleTo;
  DateTime? costFrom, costTo;
  DateTime? driverFrom, driverTo;

  @override
  void initState() {
    super.initState();
    _loadAllReports();
  }

  Future<void> _loadAllReports() async {
    _loadVehicleReport();
    _loadCostReport();
    _loadDriverReport();
  }

  Future<void> _loadVehicleReport() async {
    if (!mounted) return;
    setState(() => isLoadingVehicle = true);

    try {
      final response = await _apiService.getVehicleReport(
        dateFrom: vehicleFrom?.toIso8601String().split('T')[0],
        dateTo: vehicleTo?.toIso8601String().split('T')[0],
      );

      if (mounted && response['success'] == true) {
        setState(() {
          vehicleData = List<Map<String, dynamic>>.from(response['data'] ?? []);
          vehicleShown = 10;
          isLoadingVehicle = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          vehicleData = [];
          isLoadingVehicle = false;
        });
      }
    }
  }

  Future<void> _loadCostReport() async {
    if (!mounted) return;
    setState(() => isLoadingCost = true);

    try {
      final response = await _apiService.getCostReport(
        dateFrom: costFrom?.toIso8601String().split('T')[0],
        dateTo: costTo?.toIso8601String().split('T')[0],
      );

      if (mounted && response['success'] == true) {
        setState(() {
          costData = List<Map<String, dynamic>>.from(response['data'] ?? []);
          costShown = 10;
          isLoadingCost = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          costData = [];
          isLoadingCost = false;
        });
      }
    }
  }

  Future<void> _loadDriverReport() async {
    if (!mounted) return;
    setState(() => isLoadingDriver = true);

    try {
      final response = await _apiService.getDriverReport(
        dateFrom: driverFrom?.toIso8601String().split('T')[0],
        dateTo: driverTo?.toIso8601String().split('T')[0],
      );

      if (mounted && response['success'] == true) {
        setState(() {
          driverData = List<Map<String, dynamic>>.from(response['data'] ?? []);
          driverShown = 10;
          isLoadingDriver = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          driverData = [];
          isLoadingDriver = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 4,
      child: Column(
        children: [
          Container(
            color: Colors.white,
            child: const TabBar(
              labelColor: PastelColors.primary,
              unselectedLabelColor: Colors.grey,
              indicatorColor: PastelColors.primary,
              indicatorWeight: 3,
              tabs: [
                Tab(text: 'Vehicle'),
                Tab(text: 'Cost'),
                Tab(text: 'Driver'),
                Tab(text: 'Help'),
              ],
            ),
          ),
          Expanded(
            child: TabBarView(
              children: [
                _buildVehicleTab(),
                _buildCostTab(),
                _buildDriverTab(),
                _buildHelpTab(),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildVehicleTab() {
    return RefreshIndicator(
      onRefresh: _loadVehicleReport,
      child: Container(
        color: const Color(0xFFF5F5F5), // Same as Log Perjalanan
        child: ListView(
          padding: EdgeInsets.zero,
          physics: const AlwaysScrollableScrollPhysics(),
          children: [
            // Date Filters - SAMA DESIGN MACAM LOG PERJALANAN
            Container(
              color: Colors.white,
              padding: const EdgeInsets.all(16),
              child: Column(
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: _buildDateButton(
                          label: vehicleFrom != null
                              ? DateFormat('dd/MM/yyyy').format(vehicleFrom!)
                              : 'From',
                          onTap: () async {
                            DateTime? picked = await showDatePicker(
                              context: context,
                              initialDate: vehicleFrom ?? DateTime.now(),
                              firstDate: DateTime(2020),
                              lastDate: DateTime(2100),
                            );
                            if (picked != null) setState(() => vehicleFrom = picked);
                          },
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: _buildDateButton(
                          label: vehicleTo != null
                              ? DateFormat('dd/MM/yyyy').format(vehicleTo!)
                              : 'To',
                          onTap: () async {
                            DateTime? picked = await showDatePicker(
                              context: context,
                              initialDate: vehicleTo ?? DateTime.now(),
                              firstDate: DateTime(2020),
                              lastDate: DateTime(2100),
                            );
                            if (picked != null) setState(() => vehicleTo = picked);
                          },
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  SizedBox(
                    width: double.infinity,
                    height: 48,
                    child: ElevatedButton(
                      onPressed: isLoadingVehicle ? null : _loadVehicleReport,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: PastelColors.primary,
                        foregroundColor: Colors.white,
                        disabledBackgroundColor: Colors.grey[300],
                        disabledForegroundColor: Colors.grey[600],
                        elevation: 0,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                      ),
                      child: Text(
                        isLoadingVehicle ? 'Loading...' : 'Generate',
                        style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            
            // Vehicle List
            if (isLoadingVehicle)
              const Center(child: Padding(
                padding: EdgeInsets.all(48),
                child: CircularProgressIndicator(),
              ))
            else if (vehicleData == null || vehicleData!.isEmpty)
              _buildEmptyState('No vehicle data available')
            else
              ...vehicleData!.take(vehicleShown).map((v) => _buildVehicleCard(v)),
            
            // Load More Button
            if (vehicleData != null && vehicleData!.isNotEmpty)
              Padding(
                padding: const EdgeInsets.all(16),
                child: SizedBox(
                  width: double.infinity,
                  height: 48,
                  child: ElevatedButton(
                    onPressed: vehicleShown < vehicleData!.length
                        ? () => setState(() => vehicleShown += 10)
                        : null,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: PastelColors.primary,
                      foregroundColor: Colors.white,
                      disabledBackgroundColor: Colors.grey[300],
                      disabledForegroundColor: Colors.grey[600],
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                    child: Text(
                      vehicleShown < vehicleData!.length ? 'Load More' : 'Finish',
                      style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w600),
                    ),
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildCostTab() {
    return RefreshIndicator(
      onRefresh: _loadCostReport,
      child: Container(
        color: const Color(0xFFF5F5F5),
        child: ListView(
          padding: EdgeInsets.zero,
          physics: const AlwaysScrollableScrollPhysics(),
          children: [
            Container(
              color: Colors.white,
              padding: const EdgeInsets.all(16),
              child: Column(
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: _buildDateButton(
                          label: costFrom != null
                              ? DateFormat('dd/MM/yyyy').format(costFrom!)
                              : 'From',
                          onTap: () async {
                            DateTime? picked = await showDatePicker(
                              context: context,
                              initialDate: costFrom ?? DateTime.now(),
                              firstDate: DateTime(2020),
                              lastDate: DateTime(2100),
                            );
                            if (picked != null) setState(() => costFrom = picked);
                          },
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: _buildDateButton(
                          label: costTo != null
                              ? DateFormat('dd/MM/yyyy').format(costTo!)
                              : 'To',
                          onTap: () async {
                            DateTime? picked = await showDatePicker(
                              context: context,
                              initialDate: costTo ?? DateTime.now(),
                              firstDate: DateTime(2020),
                              lastDate: DateTime(2100),
                            );
                            if (picked != null) setState(() => costTo = picked);
                          },
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  SizedBox(
                    width: double.infinity,
                    height: 48,
                    child: ElevatedButton(
                      onPressed: isLoadingCost ? null : _loadCostReport,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: PastelColors.primary,
                        foregroundColor: Colors.white,
                        disabledBackgroundColor: Colors.grey[300],
                        disabledForegroundColor: Colors.grey[600],
                        elevation: 0,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                      ),
                      child: Text(
                        isLoadingCost ? 'Loading...' : 'Generate',
                        style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            
            if (isLoadingCost)
              const Center(child: Padding(
                padding: EdgeInsets.all(48),
                child: CircularProgressIndicator(),
              ))
            else if (costData == null || costData!.isEmpty)
              _buildEmptyState('No cost data available')
            else
              ...costData!.take(costShown).map((c) => _buildCostCard(c)),
            
            if (costData != null && costData!.isNotEmpty)
              Padding(
                padding: const EdgeInsets.all(16),
                child: SizedBox(
                  width: double.infinity,
                  height: 48,
                  child: ElevatedButton(
                    onPressed: costShown < costData!.length
                        ? () => setState(() => costShown += 10)
                        : null,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: PastelColors.primary,
                      foregroundColor: Colors.white,
                      disabledBackgroundColor: Colors.grey[300],
                      disabledForegroundColor: Colors.grey[600],
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                    child: Text(
                      costShown < costData!.length ? 'Load More' : 'Finish',
                      style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w600),
                    ),
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildDriverTab() {
    return RefreshIndicator(
      onRefresh: _loadDriverReport,
      child: Container(
        color: const Color(0xFFF5F5F5),
        child: ListView(
          padding: EdgeInsets.zero,
          physics: const AlwaysScrollableScrollPhysics(),
          children: [
            Container(
              color: Colors.white,
              padding: const EdgeInsets.all(16),
              child: Column(
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: _buildDateButton(
                          label: driverFrom != null
                              ? DateFormat('dd/MM/yyyy').format(driverFrom!)
                              : 'From',
                          onTap: () async {
                            DateTime? picked = await showDatePicker(
                              context: context,
                              initialDate: driverFrom ?? DateTime.now(),
                              firstDate: DateTime(2020),
                              lastDate: DateTime(2100),
                            );
                            if (picked != null) setState(() => driverFrom = picked);
                          },
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: _buildDateButton(
                          label: driverTo != null
                              ? DateFormat('dd/MM/yyyy').format(driverTo!)
                              : 'To',
                          onTap: () async {
                            DateTime? picked = await showDatePicker(
                              context: context,
                              initialDate: driverTo ?? DateTime.now(),
                              firstDate: DateTime(2020),
                              lastDate: DateTime(2100),
                            );
                            if (picked != null) setState(() => driverTo = picked);
                          },
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  SizedBox(
                    width: double.infinity,
                    height: 48,
                    child: ElevatedButton(
                      onPressed: isLoadingDriver ? null : _loadDriverReport,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: PastelColors.primary,
                        foregroundColor: Colors.white,
                        disabledBackgroundColor: Colors.grey[300],
                        disabledForegroundColor: Colors.grey[600],
                        elevation: 0,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                      ),
                      child: Text(
                        isLoadingDriver ? 'Loading...' : 'Generate',
                        style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            
            if (isLoadingDriver)
              const Center(child: Padding(
                padding: EdgeInsets.all(48),
                child: CircularProgressIndicator(),
              ))
            else if (driverData == null || driverData!.isEmpty)
              _buildEmptyState('No driver data available')
            else
              ...driverData!.take(driverShown).map((d) => _buildDriverCard(d)),
            
            if (driverData != null && driverData!.isNotEmpty)
              Padding(
                padding: const EdgeInsets.all(16),
                child: SizedBox(
                  width: double.infinity,
                  height: 48,
                  child: ElevatedButton(
                    onPressed: driverShown < driverData!.length
                        ? () => setState(() => driverShown += 10)
                        : null,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: PastelColors.primary,
                      foregroundColor: Colors.white,
                      disabledBackgroundColor: Colors.grey[300],
                      disabledForegroundColor: Colors.grey[600],
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                    child: Text(
                      driverShown < driverData!.length ? 'Load More' : 'Finish',
                      style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w600),
                    ),
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }

  // Date Button - SAMA MACAM LOG PERJALANAN
  Widget _buildDateButton({required String label, required VoidCallback onTap}) {
    final bool isPlaceholder = label == 'From' || label == 'To';
    return InkWell(
      onTap: onTap,
      child: Container(
        height: 48,
        padding: const EdgeInsets.symmetric(horizontal: 14),
        decoration: BoxDecoration(
          border: Border.all(color: const Color(0xFFE0E0E0), width: 1.5),
          borderRadius: BorderRadius.circular(8),
          color: Colors.white,
        ),
        child: Row(
          children: [
            Icon(
              Icons.calendar_today_outlined,
              size: 20,
              color: isPlaceholder ? Colors.grey[600] : PastelColors.primary,
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Text(
                label,
                style: TextStyle(
                  fontSize: 15,
                  color: isPlaceholder ? Colors.grey[600] : Colors.black87,
                  fontWeight: isPlaceholder ? FontWeight.normal : FontWeight.w500,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // Vehicle Card - MORE DETAILED
  Widget _buildVehicleCard(Map<String, dynamic> vehicle) {
    return Container(
      margin: const EdgeInsets.only(left: 16, right: 16, top: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(12),
          onTap: () => _showVehicleDetails(vehicle),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Header
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Text(
                        vehicle['program']?.toString() ?? 'Tiada Program',
                        style: const TextStyle(
                          fontSize: 17,
                          fontWeight: FontWeight.bold,
                          color: Colors.black87,
                        ),
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: Colors.green[50],
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Text(
                        'Selesai',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: Colors.green[700],
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 14),
                
                // Row 1: Tarikh & Masa
                Row(
                  children: [
                    Expanded(
                      child: _buildCardInfo(
                        Icons.calendar_today_outlined,
                        'Tarikh',
                        _formatDate(vehicle['date']?.toString() ?? ''),
                      ),
                    ),
                    Expanded(
                      child: _buildCardInfo(
                        Icons.access_time_outlined,
                        'Masa',
                        _formatTime(vehicle['date']?.toString() ?? ''),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                
                // Row 2: Kenderaan & Jarak
                Row(
                  children: [
                    Expanded(
                      child: _buildCardInfo(
                        Icons.directions_car_outlined,
                        'Kenderaan',
                        vehicle['no_plat']?.toString() ?? '-',
                      ),
                    ),
                    Expanded(
                      child: _buildCardInfo(
                        Icons.route_outlined,
                        'Jarak',
                        '${vehicle['distance']?.toString() ?? '0'} km',
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                
                // Row 3: Lokasi
                _buildCardInfo(
                  Icons.location_on_outlined,
                  'Lokasi',
                  vehicle['location']?.toString() ?? '-',
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // Cost Card - MORE DETAILED  
  Widget _buildCostCard(Map<String, dynamic> cost) {
    return Container(
      margin: const EdgeInsets.only(left: 16, right: 16, top: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(12),
          onTap: () => _showCostDetails(cost),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  cost['program']?.toString() ?? 'Tiada Program',
                  style: const TextStyle(
                    fontSize: 17,
                    fontWeight: FontWeight.bold,
                    color: Colors.black87,
                  ),
                ),
                const SizedBox(height: 14),
                
                Row(
                  children: [
                    Expanded(
                      child: _buildCardInfo(
                        Icons.calendar_today_outlined,
                        'Tarikh',
                        _formatDate(cost['date']?.toString() ?? ''),
                      ),
                    ),
                    Expanded(
                      child: _buildCardInfo(
                        Icons.access_time_outlined,
                        'Masa',
                        _formatTime(cost['date']?.toString() ?? ''),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                
                Row(
                  children: [
                    Expanded(
                      child: _buildCardInfo(
                        Icons.directions_car_outlined,
                        'Kenderaan',
                        cost['vehicle']?.toString() ?? '-',
                      ),
                    ),
                    Expanded(
                      child: _buildCardInfo(
                        Icons.water_drop_outlined,
                        'Jarak',
                        '${cost['liters']?.toString() ?? '0'} km',
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                
                // Cost Info with Blue Badge
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.blue[50],
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Row(
                    children: [
                      Icon(Icons.local_gas_station_outlined, size: 20, color: Colors.blue[700]),
                      const SizedBox(width: 8),
                      Text(
                        'Kos: ',
                        style: TextStyle(
                          fontSize: 13,
                          color: Colors.blue[700],
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                      Text(
                        'RM ${_formatCurrency(cost['amount'])}',
                        style: TextStyle(
                          fontSize: 16,
                          color: Colors.blue[700],
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const Spacer(),
                      Text(
                        'Liter: ${cost['liters']?.toString() ?? '0'} L',
                        style: TextStyle(
                          fontSize: 13,
                          color: Colors.blue[700],
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // Driver Card - MORE DETAILED
  Widget _buildDriverCard(Map<String, dynamic> driver) {
    return Container(
      margin: const EdgeInsets.only(left: 16, right: 16, top: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(12),
          onTap: () => _showDriverDetails(driver),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  driver['program_name']?.toString() ?? 'Tiada Program',
                  style: const TextStyle(
                    fontSize: 17,
                    fontWeight: FontWeight.bold,
                    color: Colors.black87,
                  ),
                ),
                const SizedBox(height: 14),
                
                Row(
                  children: [
                    Expanded(
                      child: _buildCardInfo(
                        Icons.calendar_month_outlined,
                        'Total Trips',
                        driver['total_trips']?.toString() ?? '0',
                      ),
                    ),
                    Expanded(
                      child: _buildCardInfo(
                        Icons.check_circle_outline,
                        'Selesai',
                        driver['completed_count']?.toString() ?? '0',
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                
                Row(
                  children: [
                    Expanded(
                      child: _buildCardInfo(
                        Icons.logout_outlined,
                        'Check Out',
                        driver['check_out_count']?.toString() ?? '0',
                      ),
                    ),
                    Expanded(
                      child: _buildCardInfo(
                        Icons.login_outlined,
                        'Check In',
                        driver['check_in_count']?.toString() ?? '0',
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                
                _buildCardInfo(
                  Icons.route_outlined,
                  'Jumlah Jarak',
                  '${driver['total_distance']?.toString() ?? '0'} km',
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // Card Info Widget - SAMA MACAM LOG PERJALANAN
  Widget _buildCardInfo(IconData icon, String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 18, color: Colors.grey[600]),
        const SizedBox(width: 8),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: TextStyle(
                  fontSize: 12,
                  color: Colors.grey[600],
                ),
              ),
              const SizedBox(height: 2),
              Text(
                value,
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: Colors.black87,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  // Empty State
  Widget _buildEmptyState(String message) {
    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(48),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Center(
        child: Column(
          children: [
            Icon(Icons.info_outline, size: 56, color: Colors.grey[400]),
            const SizedBox(height: 16),
            Text(
              message,
              style: TextStyle(
                fontSize: 15,
                color: Colors.grey[600],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // Detail Modals - FULL DETAIL MACAM LOG PERJALANAN
  void _showVehicleDetails(Map<String, dynamic> vehicle) {
    final programDetails = vehicle['program_details'] ?? {};
    final vehicleDetails = vehicle['vehicle_details'] ?? {};
    final journeyDetails = vehicle['journey_details'] ?? {};
    final fuelDetails = vehicle['fuel_details'] ?? {};
    final images = vehicle['images'] ?? {};
    
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        height: MediaQuery.of(context).size.height * 0.75,
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
        ),
        child: Column(
          children: [
            // Handle bar
            Container(
              margin: const EdgeInsets.symmetric(vertical: 12),
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: Colors.grey[300],
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            
            // Header with X button
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Butiran Log',
                    style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.black87),
                  ),
                  IconButton(
                    onPressed: () => Navigator.pop(context),
                    icon: const Icon(Icons.close),
                    color: Colors.grey[600],
                  ),
                ],
              ),
            ),
            
            const Divider(),
            
            // Content
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Program Section
                    _buildSectionTitle('Program'),
                    const SizedBox(height: 12),
                    _buildDetailContainer([
                      _buildDetailRow('Nama Program', programDetails['nama_program']?.toString() ?? '-'),
                      _buildDetailRow('Lokasi', programDetails['lokasi_program']?.toString() ?? '-'),
                      if (programDetails['permohonan_dari'] != null)
                        _buildDetailRow('Permohonan Dari', programDetails['permohonan_dari']?.toString() ?? '-'),
                    ]),
                    
                    // Kenderaan Section
                    const SizedBox(height: 20),
                    _buildSectionTitle('Kenderaan'),
                    const SizedBox(height: 12),
                    _buildDetailContainer([
                      _buildDetailRow('No. Plat', vehicleDetails['no_plat']?.toString() ?? '-'),
                      _buildDetailRow('Jenama/Model', '${vehicleDetails['jenama'] ?? '-'} ${vehicleDetails['model'] ?? ''}'),
                      if (vehicleDetails['jenis_bahan_api'] != null)
                        _buildDetailRow('Jenis Bahan Api', vehicleDetails['jenis_bahan_api']?.toString() ?? '-'),
                    ]),
                    
                    // Perjalanan Section
                    const SizedBox(height: 20),
                    _buildSectionTitle('Perjalanan'),
                    const SizedBox(height: 12),
                    _buildDetailContainer([
                      _buildDetailRow('Tarikh', _formatDate(journeyDetails['tarikh']?.toString() ?? '')),
                      _buildDetailRow('Masa Keluar', _formatFullTime(journeyDetails['masa_keluar']?.toString() ?? '')),
                      _buildDetailRow('Masa Masuk', _formatFullTime(journeyDetails['masa_masuk']?.toString() ?? '')),
                      _buildDetailRow('Odometer Keluar', '${journeyDetails['odometer_keluar']?.toString() ?? '-'} km'),
                      _buildDetailRow('Odometer Masuk', '${journeyDetails['odometer_masuk']?.toString() ?? '-'} km'),
                      _buildDetailRow('Jarak Sebenar', '${journeyDetails['jarak']?.toString() ?? '0'} km'),
                      if (journeyDetails['catatan'] != null && journeyDetails['catatan'].toString().isNotEmpty)
                        _buildDetailRow('Catatan', journeyDetails['catatan']?.toString() ?? '-'),
                    ]),
                    
                    // Bahan Api Section
                    if (fuelDetails['kos_minyak'] != null)
                      ...[
                        const SizedBox(height: 20),
                        _buildSectionTitle('Bahan Api'),
                        const SizedBox(height: 12),
                        _buildDetailContainer([
                          _buildDetailRow('Kos Minyak', 'RM ${_formatCurrency(fuelDetails['kos_minyak'])}'),
                          if (fuelDetails['liter_minyak'] != null)
                            _buildDetailRow('Liter', '${fuelDetails['liter_minyak']?.toString() ?? '0'} L'),
                          if (fuelDetails['stesen_minyak'] != null)
                            _buildDetailRow('Stesen Minyak', fuelDetails['stesen_minyak']?.toString() ?? '-'),
                        ]),
                      ],
                    
                    // Images Section
                    if (images['foto_odometer_keluar'] != null || images['foto_odometer_masuk'] != null || images['resit_minyak'] != null)
                      ...[
                        const SizedBox(height: 20),
                        _buildSectionTitle('Gambar'),
                        const SizedBox(height: 12),
                        if (images['foto_odometer_keluar'] != null)
                          _buildImagePreview('Gambar Check-out (Start Journey)', images['foto_odometer_keluar']),
                        if (images['foto_odometer_masuk'] != null)
                          _buildImagePreview('Gambar Check-in (End Journey)', images['foto_odometer_masuk']),
                        if (images['resit_minyak'] != null)
                          _buildImagePreview('Resit Minyak', images['resit_minyak']),
                      ],
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _showCostDetails(Map<String, dynamic> cost) {
    final programDetails = cost['program_details'] ?? {};
    final vehicleDetails = cost['vehicle_details'] ?? {};
    final journeyDetails = cost['journey_details'] ?? {};
    final fuelDetails = cost['fuel_details'] ?? {};
    final images = cost['images'] ?? {};
    
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        height: MediaQuery.of(context).size.height * 0.75,
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
        ),
        child: Column(
          children: [
            // Handle bar
            Container(
              margin: const EdgeInsets.symmetric(vertical: 12),
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: Colors.grey[300],
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            
            // Header with X button
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Butiran Log',
                    style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.black87),
                  ),
                  IconButton(
                    onPressed: () => Navigator.pop(context),
                    icon: const Icon(Icons.close),
                    color: Colors.grey[600],
                  ),
                ],
              ),
            ),
            
            const Divider(),
            
            // Content
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Program Section
                    _buildSectionTitle('Program'),
                    const SizedBox(height: 12),
                    _buildDetailContainer([
                      _buildDetailRow('Nama Program', programDetails['nama_program']?.toString() ?? '-'),
                      _buildDetailRow('Lokasi', programDetails['lokasi_program']?.toString() ?? '-'),
                      if (programDetails['permohonan_dari'] != null)
                        _buildDetailRow('Permohonan Dari', programDetails['permohonan_dari']?.toString() ?? '-'),
                    ]),
                    
                    // Kenderaan Section
                    const SizedBox(height: 20),
                    _buildSectionTitle('Kenderaan'),
                    const SizedBox(height: 12),
                    _buildDetailContainer([
                      _buildDetailRow('No. Plat', vehicleDetails['no_plat']?.toString() ?? '-'),
                      _buildDetailRow('Jenama/Model', '${vehicleDetails['jenama'] ?? '-'} ${vehicleDetails['model'] ?? ''}'),
                    ]),
                    
                    // Perjalanan Section
                    const SizedBox(height: 20),
                    _buildSectionTitle('Perjalanan'),
                    const SizedBox(height: 12),
                    _buildDetailContainer([
                      _buildDetailRow('Tarikh', _formatDate(journeyDetails['tarikh']?.toString() ?? '')),
                      _buildDetailRow('Masa Keluar', _formatFullTime(journeyDetails['masa_keluar']?.toString() ?? '')),
                      _buildDetailRow('Masa Masuk', _formatFullTime(journeyDetails['masa_masuk']?.toString() ?? '')),
                      _buildDetailRow('Odometer Keluar', '${journeyDetails['odometer_keluar']?.toString() ?? '-'} km'),
                      _buildDetailRow('Odometer Masuk', '${journeyDetails['odometer_masuk']?.toString() ?? '-'} km'),
                      _buildDetailRow('Jarak Sebenar', '${journeyDetails['jarak']?.toString() ?? '0'} km'),
                    ]),
                    
                    // Bahan Api Section - HIGHLIGHTED
                    const SizedBox(height: 20),
                    _buildSectionTitle('Bahan Api'),
                    const SizedBox(height: 12),
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.blue[50],
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: Colors.blue[200]!, width: 1),
                      ),
                      child: Column(
                        children: [
                          _buildDetailRow('Kos Minyak', 'RM ${_formatCurrency(fuelDetails['kos_minyak'])}', isHighlighted: true),
                          _buildDetailRow('Liter', '${_formatCurrency(fuelDetails['liter_minyak'])} L', isHighlighted: true),
                          if (fuelDetails['stesen_minyak'] != null)
                            _buildDetailRow('Stesen Minyak', fuelDetails['stesen_minyak']?.toString() ?? '-', isHighlighted: true),
                        ],
                      ),
                    ),
                    
                    // Images Section
                    if (images['foto_odometer_keluar'] != null || images['foto_odometer_masuk'] != null || images['resit_minyak'] != null)
                      ...[
                        const SizedBox(height: 20),
                        _buildSectionTitle('Gambar'),
                        const SizedBox(height: 12),
                        if (images['foto_odometer_keluar'] != null)
                          _buildImagePreview('Gambar Check-out (Start Journey)', images['foto_odometer_keluar']),
                        if (images['foto_odometer_masuk'] != null)
                          _buildImagePreview('Gambar Check-in (End Journey)', images['foto_odometer_masuk']),
                        if (images['resit_minyak'] != null)
                          _buildImagePreview('Resit Minyak', images['resit_minyak']),
                      ],
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _showDriverDetails(Map<String, dynamic> driver) {
    final trips = List<Map<String, dynamic>>.from(driver['trips'] ?? []);
    
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        height: MediaQuery.of(context).size.height * 0.75,
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
        ),
        child: Column(
          children: [
            // Handle bar
            Container(
              margin: const EdgeInsets.symmetric(vertical: 12),
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: Colors.grey[300],
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            
            // Header with X button
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Butiran Log',
                    style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.black87),
                  ),
                  IconButton(
                    onPressed: () => Navigator.pop(context),
                    icon: const Icon(Icons.close),
                    color: Colors.grey[600],
                  ),
                ],
              ),
            ),
            
            const Divider(),
            
            // Content
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Program Section
                    _buildSectionTitle('Program'),
                    const SizedBox(height: 12),
                    _buildDetailContainer([
                      _buildDetailRow('Nama Program', driver['program_name']?.toString() ?? '-'),
                      if (driver['program_location'] != null)
                        _buildDetailRow('Lokasi', driver['program_location']?.toString() ?? '-'),
                      if (driver['permohonan_dari'] != null)
                        _buildDetailRow('Permohonan Dari', driver['permohonan_dari']?.toString() ?? '-'),
                    ]),
                    
                    // Summary Section
                    const SizedBox(height: 20),
                    _buildSectionTitle('Ringkasan'),
                    const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.blue[50],
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.blue[200]!, width: 1),
                ),
                child: Column(
                  children: [
                    Row(
                      children: [
                        Expanded(
                          child: _buildSummaryCard(
                            'Total Trips',
                            driver['total_trips']?.toString() ?? '0',
                            Icons.calendar_month_outlined,
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: _buildSummaryCard(
                            'Selesai',
                            driver['completed_count']?.toString() ?? '0',
                            Icons.check_circle_outline,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        Expanded(
                          child: _buildSummaryCard(
                            'Jarak',
                            '${driver['total_distance']?.toString() ?? '0'} km',
                            Icons.route_outlined,
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: _buildSummaryCard(
                            'Kos Minyak',
                            'RM ${_formatCurrency(driver['total_fuel_cost'])}',
                            Icons.local_gas_station_outlined,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              
              // All Trips Section - DETAILED
              if (trips.isNotEmpty)
                ...[
                  const SizedBox(height: 20),
                  _buildSectionTitle('Senarai Perjalanan (${trips.length})'),
                  const SizedBox(height: 12),
                  ...trips.asMap().entries.map((entry) {
                    final index = entry.key;
                    final trip = entry.value;
                    return Padding(
                      padding: const EdgeInsets.only(bottom: 12),
                      child: Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.grey[50],
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.grey[300]!, width: 1),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Trip Header
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text(
                                  'Perjalanan ${index + 1}',
                                  style: const TextStyle(
                                    fontSize: 15,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.black87,
                                  ),
                                ),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                  decoration: BoxDecoration(
                                    color: trip['status'] == 'selesai' ? Colors.green[50] : Colors.orange[50],
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  child: Text(
                                    trip['status'] == 'selesai' ? 'Selesai' : 'Dalam Perjalanan',
                                    style: TextStyle(
                                      fontSize: 11,
                                      fontWeight: FontWeight.w600,
                                      color: trip['status'] == 'selesai' ? Colors.green[700] : Colors.orange[700],
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const Divider(height: 20),
                            
                            // Trip Details
                            _buildTripDetailRow(Icons.calendar_today_outlined, 'Tarikh', _formatDate(trip['tarikh']?.toString() ?? '')),
                            const SizedBox(height: 10),
                            _buildTripDetailRow(Icons.logout_outlined, 'Masa Keluar', _formatFullTime(trip['masa_keluar']?.toString() ?? '')),
                            const SizedBox(height: 10),
                            _buildTripDetailRow(Icons.login_outlined, 'Masa Masuk', _formatFullTime(trip['masa_masuk']?.toString() ?? '')),
                            const SizedBox(height: 10),
                            _buildTripDetailRow(Icons.directions_car_outlined, 'Kenderaan', trip['kenderaan']?.toString() ?? '-'),
                            const SizedBox(height: 10),
                            _buildTripDetailRow(Icons.route_outlined, 'Jarak', '${trip['jarak']?.toString() ?? '0'} km'),
                            
                            // Fuel Info if available
                            if (trip['kos_minyak'] != null)
                              ...[
                                const SizedBox(height: 10),
                                Container(
                                  padding: const EdgeInsets.all(10),
                                  decoration: BoxDecoration(
                                    color: Colors.blue[50],
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                  child: Row(
                                    children: [
                                      Icon(Icons.local_gas_station_outlined, size: 16, color: Colors.blue[700]),
                                      const SizedBox(width: 8),
                                      Text(
                                        'Kos Minyak: ',
                                        style: TextStyle(fontSize: 13, color: Colors.blue[700]),
                                      ),
                                      Text(
                                        'RM ${_formatCurrency(trip['kos_minyak'])}',
                                        style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: Colors.blue[700]),
                                      ),
                                      if (trip['liter_minyak'] != null)
                                        ...[
                                          const Text(' • ', style: TextStyle(color: Colors.blue)),
                                          Text(
                                            '${_formatCurrency(trip['liter_minyak'])} L',
                                            style: TextStyle(fontSize: 13, color: Colors.blue[700]),
                                          ),
                                        ],
                                    ],
                                  ),
                                ),
                              ],
                          ],
                        ),
                      ),
                    );
                  }),
                ],
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // Helper Widgets for Detail Modals
  Widget _buildSectionTitle(String title) {
    return Text(
      title,
      style: const TextStyle(
        fontSize: 16,
        fontWeight: FontWeight.bold,
        color: Colors.black87,
      ),
    );
  }

  Widget _buildDetailContainer(List<Widget> children) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[50],
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey[300]!, width: 1),
      ),
      child: Column(
        children: children,
      ),
    );
  }

  Widget _buildDetailRow(String label, String value, {bool isHighlighted = false}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 110,
            child: Text(
              label,
              style: TextStyle(
                fontSize: 13,
                color: isHighlighted ? Colors.blue[700] : Colors.grey[600],
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: TextStyle(
                fontSize: 14,
                color: isHighlighted ? Colors.blue[900] : Colors.black87,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTripDetailRow(IconData icon, String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 18, color: Colors.grey[600]),
        const SizedBox(width: 10),
        SizedBox(
          width: 95,
          child: Text(
            label,
            style: TextStyle(
              fontSize: 13,
              color: Colors.grey[600],
            ),
          ),
        ),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w600,
              color: Colors.black87,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildSummaryCard(String label, String value, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Column(
        children: [
          Icon(icon, size: 24, color: Colors.blue[700]),
          const SizedBox(height: 8),
          Text(
            value,
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: Colors.blue[900],
            ),
          ),
          const SizedBox(height: 4),
          Text(
            label,
            style: TextStyle(
              fontSize: 12,
              color: Colors.grey[600],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildImagePreview(String label, String? imageUrl) {
    if (imageUrl == null) return const SizedBox.shrink();
    
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w600,
              color: Colors.black87,
            ),
          ),
          const SizedBox(height: 8),
          ClipRRect(
            borderRadius: BorderRadius.circular(8),
            child: Image.network(
              imageUrl,
              height: 200,
              width: double.infinity,
              fit: BoxFit.cover,
              errorBuilder: (context, error, stackTrace) => Container(
                height: 200,
                width: double.infinity,
                color: Colors.grey[200],
                child: const Center(
                  child: Icon(Icons.broken_image_outlined, size: 48, color: Colors.grey),
                ),
              ),
              loadingBuilder: (context, child, loadingProgress) {
                if (loadingProgress == null) return child;
                return Container(
                  height: 200,
                  width: double.infinity,
                  color: Colors.grey[200],
                  child: Center(
                    child: CircularProgressIndicator(
                      value: loadingProgress.expectedTotalBytes != null
                          ? loadingProgress.cumulativeBytesLoaded / loadingProgress.expectedTotalBytes!
                          : null,
                    ),
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildHelpTab() {
    return Container(
      color: const Color(0xFFF5F5F5),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Container(
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.05),
                blurRadius: 8,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: ListView(
            padding: const EdgeInsets.all(16),
            children: [
              const _HelpExpansionTileList(),
              const SizedBox(height: 24),
              Text(
                'Submit Report',
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 16,
                  color: PastelColors.primary,
                ),
              ),
              const SizedBox(height: 12),
              TextField(
                style: AppTextStyles.bodyMedium,
                decoration: InputDecoration(
                  labelText: 'Subject',
                  labelStyle: AppTextStyles.bodyLarge,
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                  isDense: true,
                ),
              ),
              const SizedBox(height: 12),
              TextField(
                style: AppTextStyles.bodyMedium,
                decoration: InputDecoration(
                  labelText: 'Category',
                  labelStyle: AppTextStyles.bodyLarge,
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                  isDense: true,
                ),
              ),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                dropdownColor: Colors.white,
                decoration: InputDecoration(
                  labelText: 'Priority',
                  labelStyle: AppTextStyles.bodyLarge,
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                  isDense: true,
                ),
                items: const [
                  DropdownMenuItem(value: 'Low', child: Text('Low')),
                  DropdownMenuItem(value: 'High', child: Text('High')),
                  DropdownMenuItem(value: 'Critical', child: Text('Critical')),
                ],
                onChanged: (v) {},
                style: AppTextStyles.bodyMedium,
              ),
              const SizedBox(height: 12),
              TextField(
                style: AppTextStyles.bodyMedium,
                minLines: 3,
                maxLines: 5,
                decoration: InputDecoration(
                  labelText: 'Message',
                  labelStyle: AppTextStyles.bodyLarge,
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                  isDense: true,
                ),
              ),
              const SizedBox(height: 16),
              SizedBox(
                width: double.infinity,
                height: 48,
                child: ElevatedButton(
                  onPressed: () {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                        content: Text('Report submitted! (Will integrate with live chat)'),
                        backgroundColor: Colors.green,
                      ),
                    );
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: PastelColors.primary,
                    foregroundColor: Colors.white,
                    elevation: 0,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                  ),
                  child: const Text('Submit', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // Helper methods
  String _formatDate(String dateStr) {
    try {
      final date = DateTime.parse(dateStr);
      return DateFormat('dd/MM/yyyy').format(date);
    } catch (e) {
      return 'N/A';
    }
  }

  String _formatTime(String dateStr) {
    try {
      final date = DateTime.parse(dateStr);
      return DateFormat('HH:mm').format(date);
    } catch (e) {
      return 'N/A';
    }
  }

  String _formatFullTime(String dateStr) {
    try {
      final date = DateTime.parse(dateStr);
      return DateFormat('dd/MM/yyyy HH:mm').format(date);
    } catch (e) {
      return 'N/A';
    }
  }

  String _formatCurrency(dynamic amount) {
    try {
      final num = double.parse(amount.toString());
      return num.toStringAsFixed(2);
    } catch (e) {
      return '0.00';
    }
  }
}

// FAQ Expansion Tile List
class _HelpExpansionTileList extends StatefulWidget {
  const _HelpExpansionTileList();

  @override
  State<_HelpExpansionTileList> createState() => _HelpExpansionTileListState();
}

class _HelpExpansionTileListState extends State<_HelpExpansionTileList> {
  final List<Map<String, String>> _faq = [
    {
      'q': 'How to check in?',
      'a': 'To check in, go to the Do tab and tap the Check In card. You will be prompted to confirm your location and time. Make sure your GPS is enabled.\n\nSteps:\n• Open Do tab\n• Tap Check In\n• Confirm details\n• Submit',
    },
    {
      'q': 'How to claim cost?',
      'a': 'To claim cost, go to the Do tab and tap the Claim card. Fill in the required details and upload your receipt if needed.\n\nTips:\n• Ensure all fields are filled\n• Attach clear photo of receipt',
    },
    {
      'q': 'Who to contact for issues?',
      'a': 'For technical issues, please contact RISDA admin at 03-8888 8888 or email support@risda.gov.my.\n\nSupport hours: 8am - 5pm (Mon-Fri)',
    },
    {
      'q': 'How to view my program history?',
      'a': 'You can view your program history in the Overview or Report tab. Filter by date to see past programs.\n\n• Overview tab: quick stats\n• Report tab: detailed list',
    },
    {
      'q': 'How to reset my password?',
      'a': 'Go to Profile > Settings and select Reset Password. Follow the instructions sent to your email.\n\nIf you do not receive an email, check your spam folder.',
    },
  ];

  List<bool> _expanded = [false, false, false, false, false];

  @override
  Widget build(BuildContext context) {
    return ListView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: _faq.length,
      itemBuilder: (context, i) {
        return Card(
          margin: const EdgeInsets.only(bottom: 8),
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
            side: BorderSide(color: Colors.grey[300]!),
          ),
          child: Theme(
            data: Theme.of(context).copyWith(dividerColor: Colors.transparent),
            child: ExpansionTile(
              tilePadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
              childrenPadding: const EdgeInsets.only(left: 16, right: 16, bottom: 16, top: 0),
              title: Text(
                _faq[i]['q']!,
                style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600),
              ),
              trailing: AnimatedRotation(
                turns: _expanded[i] ? 0.5 : 0.0,
                duration: const Duration(milliseconds: 200),
                child: const Icon(Icons.keyboard_arrow_down_rounded, size: 24),
              ),
              onExpansionChanged: (expanded) {
                setState(() {
                  _expanded[i] = expanded;
                });
              },
              initiallyExpanded: _expanded[i],
              children: [
                Text(_faq[i]['a']!, style: AppTextStyles.bodyMedium),
              ],
            ),
          ),
        );
      },
    );
  }
}
