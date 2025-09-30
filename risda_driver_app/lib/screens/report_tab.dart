import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';

class ReportTab extends StatefulWidget {
  const ReportTab({super.key});

  @override
  State<ReportTab> createState() => _ReportTabState();
}

class _ReportTabState extends State<ReportTab> {
  // 🎨 DUMMY DATA - Will be replaced with API data later
  final List<Map<String, String>> vehicleData = List.generate(
    15,
    (i) => {
      'noPlate': 'QAA${1000 + i}',
      'program': 'Program ${String.fromCharCode(65 + (i % 3))}',
      'location': ['Kuching', 'Sibu', 'Miri'][i % 3],
      'distance': (80 + i * 5).toString(),
    },
  );

  final List<Map<String, String>> costData = List.generate(
    13,
    (i) => {
      'date': '2024-07-${(i + 1).toString().padLeft(2, '0')}',
      'vehicle': 'QAA${1000 + (i % 5)}',
      'program': 'Program ${String.fromCharCode(65 + (i % 3))}',
      'amount': (30 + i * 7).toStringAsFixed(2),
    },
  );

  final List<Map<String, String>> driverData = List.generate(
    7,
    (i) => {
      'programs': (3 + i).toString(),
      'checkin': (7 + i * 2).toString(),
      'checkout': (7 + i * 2 - (i % 2)).toString(),
      'status': i < 6 ? 'Active' : 'Retired',
    },
  );

  // Pagination state
  int vehicleShown = 10;
  int costShown = 10;
  int driverShown = 7;

  // Date filter state
  DateTime? vehicleFrom, vehicleTo;
  DateTime? costFrom, costTo;
  DateTime? driverFrom, driverTo;

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 4,
      child: Column(
        children: [
          Container(
            color: PastelColors.background,
            child: const TabBar(
              labelColor: PastelColors.primary,
              unselectedLabelColor: PastelColors.textSecondary,
              indicatorColor: PastelColors.primary,
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
      onRefresh: () async {
        await Future.delayed(const Duration(seconds: 1));
        if (mounted) {
          setState(() {
            vehicleData.shuffle();
          });
        }
      },
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Card(
          color: PastelColors.cardBackground,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(3),
            side: BorderSide(color: PastelColors.border, width: 1),
          ),
          child: Column(
            children: [
              // Date Filters and Generate Button
              Row(
                children: [
                  Expanded(
                    child: Padding(
                      padding: const EdgeInsets.only(left: 8, top: 12),
                      child: _DatePickerField(
                        hint: 'Search From',
                        selected: vehicleFrom,
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
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Padding(
                      padding: const EdgeInsets.only(top: 12),
                      child: _DatePickerField(
                        hint: 'Search To',
                        selected: vehicleTo,
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
                  ),
                  const SizedBox(width: 8),
                  Padding(
                    padding: const EdgeInsets.only(right: 8, top: 12),
                    child: ElevatedButton(
                      onPressed: () {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(
                            content: Text('Report generated! (Dummy Mode)'),
                            backgroundColor: Colors.green,
                          ),
                        );
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: PastelColors.primary,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                        textStyle: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                      ),
                      child: const Text('Generate Report'),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              // DataTable
              Expanded(
                child: ListView(
                  physics: const AlwaysScrollableScrollPhysics(),
                  children: [
                    SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: DataTable(
                        headingTextStyle: AppTextStyles.bodyMedium.copyWith(
                          fontWeight: FontWeight.bold,
                        ),
                        dataTextStyle: AppTextStyles.bodyMedium,
                        columns: const [
                          DataColumn(label: Text('No Plate')),
                          DataColumn(label: Text('Program')),
                          DataColumn(label: Text('Location')),
                          DataColumn(label: Text('KM')),
                        ],
                        rows: [
                          for (var v in vehicleData.take(vehicleShown))
                            DataRow(cells: [
                              DataCell(Text(v['noPlate']!)),
                              DataCell(Text(v['program']!)),
                              DataCell(Text(v['location']!)),
                              DataCell(Text(v['distance']!)),
                            ]),
                        ],
                      ),
                    ),
                    const SizedBox(height: 8),
                    Align(
                      alignment: Alignment.center,
                      child: ElevatedButton(
                        onPressed: vehicleShown < vehicleData.length
                            ? () => setState(() => vehicleShown = (vehicleShown + 10).clamp(0, vehicleData.length))
                            : null,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: vehicleShown < vehicleData.length ? PastelColors.primary : PastelColors.textLight,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                          textStyle: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                        ),
                        child: Text(vehicleShown < vehicleData.length ? 'Load More' : 'Finish'),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildCostTab() {
    return RefreshIndicator(
      onRefresh: () async {
        await Future.delayed(const Duration(seconds: 1));
        if (mounted) {
          setState(() {
            costData.shuffle();
          });
        }
      },
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Card(
          color: PastelColors.cardBackground,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(3),
            side: BorderSide(color: PastelColors.border, width: 1),
          ),
          child: Column(
            children: [
              Row(
                children: [
                  Expanded(
                    child: Padding(
                      padding: const EdgeInsets.only(left: 8, top: 12),
                      child: _DatePickerField(
                        hint: 'Search From',
                        selected: costFrom,
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
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Padding(
                      padding: const EdgeInsets.only(top: 12),
                      child: _DatePickerField(
                        hint: 'Search To',
                        selected: costTo,
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
                  ),
                  const SizedBox(width: 8),
                  Padding(
                    padding: const EdgeInsets.only(right: 8, top: 12),
                    child: ElevatedButton(
                      onPressed: () {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(
                            content: Text('Report generated! (Dummy Mode)'),
                            backgroundColor: Colors.green,
                          ),
                        );
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: PastelColors.primary,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                        textStyle: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                      ),
                      child: const Text('Generate Report'),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Expanded(
                child: ListView(
                  physics: const AlwaysScrollableScrollPhysics(),
                  children: [
                    SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: DataTable(
                        headingTextStyle: AppTextStyles.bodyMedium.copyWith(
                          fontWeight: FontWeight.bold,
                        ),
                        dataTextStyle: AppTextStyles.bodyMedium,
                        columns: const [
                          DataColumn(label: Text('Cost Date')),
                          DataColumn(label: Text('Vehicle')),
                          DataColumn(label: Text('Program')),
                          DataColumn(label: Text('RM')),
                        ],
                        rows: [
                          for (var c in costData.take(costShown))
                            DataRow(cells: [
                              DataCell(Text(c['date']!)),
                              DataCell(Text(c['vehicle']!)),
                              DataCell(Text(c['program']!)),
                              DataCell(Text(c['amount']!)),
                            ]),
                        ],
                      ),
                    ),
                    const SizedBox(height: 8),
                    Align(
                      alignment: Alignment.center,
                      child: ElevatedButton(
                        onPressed: costShown < costData.length
                            ? () => setState(() => costShown = (costShown + 10).clamp(0, costData.length))
                            : null,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: costShown < costData.length ? PastelColors.primary : PastelColors.textLight,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                          textStyle: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                        ),
                        child: Text(costShown < costData.length ? 'Load More' : 'Finish'),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildDriverTab() {
    return RefreshIndicator(
      onRefresh: () async {
        await Future.delayed(const Duration(seconds: 1));
        if (mounted) {
          setState(() {
            driverData.shuffle();
          });
        }
      },
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Card(
          color: PastelColors.cardBackground,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(3),
            side: BorderSide(color: PastelColors.border, width: 1),
          ),
          child: Column(
            children: [
              Row(
                children: [
                  Expanded(
                    child: Padding(
                      padding: const EdgeInsets.only(left: 8, top: 12),
                      child: _DatePickerField(
                        hint: 'Search From',
                        selected: driverFrom,
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
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Padding(
                      padding: const EdgeInsets.only(top: 12),
                      child: _DatePickerField(
                        hint: 'Search To',
                        selected: driverTo,
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
                  ),
                  const SizedBox(width: 8),
                  Padding(
                    padding: const EdgeInsets.only(right: 8, top: 12),
                    child: ElevatedButton(
                      onPressed: () {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(
                            content: Text('Report generated! (Dummy Mode)'),
                            backgroundColor: Colors.green,
                          ),
                        );
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: PastelColors.primary,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                        textStyle: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                      ),
                      child: const Text('Generate Report'),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Expanded(
                child: ListView(
                  physics: const AlwaysScrollableScrollPhysics(),
                  children: [
                    SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: DataTable(
                        headingTextStyle: AppTextStyles.bodyMedium.copyWith(
                          fontWeight: FontWeight.bold,
                        ),
                        dataTextStyle: AppTextStyles.bodyMedium,
                        columns: const [
                          DataColumn(label: Text('Program')),
                          DataColumn(label: Text('Check In')),
                          DataColumn(label: Text('Check Out')),
                          DataColumn(label: Text('Status')),
                        ],
                        rows: [
                          for (var d in driverData.take(driverShown))
                            DataRow(cells: [
                              DataCell(Text(d['programs']!)),
                              DataCell(Text(d['checkin']!)),
                              DataCell(Text(d['checkout']!)),
                              DataCell(Text(d['status']!)),
                            ]),
                        ],
                      ),
                    ),
                    const SizedBox(height: 8),
                    Align(
                      alignment: Alignment.center,
                      child: ElevatedButton(
                        onPressed: null,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: PastelColors.textLight,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                          textStyle: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                        ),
                        child: const Text('Finish'),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHelpTab() {
    return RefreshIndicator(
      onRefresh: () async {
        await Future.delayed(const Duration(seconds: 1));
        if (mounted) {
          setState(() {});
        }
      },
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Card(
          color: PastelColors.cardBackground,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(3),
            side: BorderSide(color: PastelColors.border, width: 1),
          ),
          child: ListView(
            physics: const AlwaysScrollableScrollPhysics(),
            children: [
              // FAQ Accordion
              const _HelpExpansionTileList(),
              const SizedBox(height: 24),
              // Submit Report Form
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                child: Text(
                  'Submit Report',
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 16,
                    color: PastelColors.primary,
                  ),
                ),
              ),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                child: TextField(
                  style: AppTextStyles.bodyMedium,
                  decoration: InputDecoration(
                    labelText: 'Subject',
                    labelStyle: AppTextStyles.bodyLarge,
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                  ),
                ),
              ),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                child: TextField(
                  style: AppTextStyles.bodyMedium,
                  decoration: InputDecoration(
                    labelText: 'Category',
                    labelStyle: AppTextStyles.bodyLarge,
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                  ),
                ),
              ),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                child: DropdownButtonFormField<String>(
                  dropdownColor: PastelColors.success,
                  decoration: InputDecoration(
                    labelText: 'Status',
                    labelStyle: AppTextStyles.bodyLarge,
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
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
              ),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                child: TextField(
                  style: AppTextStyles.bodyMedium,
                  minLines: 3,
                  maxLines: 5,
                  decoration: InputDecoration(
                    labelText: 'Message',
                    labelStyle: AppTextStyles.bodyLarge,
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
                    isDense: true,
                  ),
                ),
              ),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                child: ElevatedButton(
                  onPressed: () {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                        content: Text('Report submitted! (Dummy Mode)'),
                        backgroundColor: Colors.green,
                      ),
                    );
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: PastelColors.primary,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
                    textStyle: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(3)),
                  ),
                  child: const Text('Submit'),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// Date Picker Field Widget
class _DatePickerField extends StatelessWidget {
  final String hint;
  final DateTime? selected;
  final VoidCallback onTap;

  const _DatePickerField({
    required this.hint,
    required this.selected,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final formatter = DateFormat('yyyy-MM-dd');
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(3),
      child: InputDecorator(
        decoration: InputDecoration(
          hintText: hint,
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(3)),
          isDense: true,
          contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
        ),
        child: Text(
          selected != null ? formatter.format(selected!) : hint,
          style: const TextStyle(fontSize: 13),
        ),
      ),
    );
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
          margin: const EdgeInsets.symmetric(vertical: 2, horizontal: 0),
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(3),
            side: BorderSide(color: PastelColors.border),
          ),
          child: Theme(
            data: Theme.of(context).copyWith(dividerColor: Colors.transparent),
            child: ExpansionTile(
              tilePadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 0),
              childrenPadding: const EdgeInsets.only(left: 20, right: 16, bottom: 12, top: 0),
              title: Text(
                _faq[i]['q']!,
                style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600),
              ),
              trailing: AnimatedRotation(
                turns: _expanded[i] ? 0.5 : 0.0,
                duration: const Duration(milliseconds: 200),
                child: const Icon(Icons.keyboard_arrow_down_rounded, size: 22),
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
