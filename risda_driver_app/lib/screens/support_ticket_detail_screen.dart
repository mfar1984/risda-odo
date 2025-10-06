import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:geolocator/geolocator.dart';
import 'dart:io';
import 'dart:async';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/api_service.dart';
import '../core/api_client.dart';
import '../models/support_ticket.dart';
import 'dart:developer' as developer;

class SupportTicketDetailScreen extends StatefulWidget {
  final int ticketId;
  final String ticketNumber;

  const SupportTicketDetailScreen({
    super.key,
    required this.ticketId,
    required this.ticketNumber,
  });

  @override
  State<SupportTicketDetailScreen> createState() => _SupportTicketDetailScreenState();
}

class _SupportTicketDetailScreenState extends State<SupportTicketDetailScreen> {
  final ApiService _apiService = ApiService(ApiClient());
  final TextEditingController _messageController = TextEditingController();
  final ScrollController _scrollController = ScrollController();
  final ImagePicker _picker = ImagePicker();
  
  SupportTicket? _ticket;
  List<SupportMessage> _messages = [];
  List<File> _attachments = [];
  bool _isLoading = true;
  bool _isSending = false;
  Timer? _pollingTimer;

  @override
  void initState() {
    super.initState();
    // Load real data from API
    _loadTicketDetail();
    _startPolling();
    
    // For mockup testing only:
    // _loadMockupData();
  }

  void _loadMockupData() {
    setState(() {
      _ticket = SupportTicket(
        id: 1,
        ticketNumber: widget.ticketNumber,
        subject: 'GPS Not Working',
        category: 'teknikal',
        priority: 'tinggi',
        status: 'dalam_proses',
        statusLabel: 'In Progress',
        priorityLabel: 'High',
        messageCount: 4,
        createdAt: DateTime.now().subtract(const Duration(hours: 2)),
      );

      _messages = [
        SupportMessage(
          id: 1,
          message: 'GPS tidak dapat detect lokasi saya. Tolong bantu!',
          role: 'pengguna',
          attachments: ['screenshot.jpg'],
          location: 'Sibu, Sarawak',
          createdAt: DateTime.now().subtract(const Duration(hours: 2)),
          user: {'id': 10, 'name': 'Fairiz Bin Rahman'},
        ),
        SupportMessage(
          id: 2,
          message: 'Terima kasih atas laporan. Sila check Settings > Location. Pastikan GPS enabled.',
          role: 'admin',
          location: 'HQ Office',
          createdAt: DateTime.now().subtract(const Duration(hours: 1, minutes: 45)),
          user: {'id': 1, 'name': 'Admin Staff'},
        ),
        SupportMessage(
          id: 3,
          message: 'Dah check, masih sama. GPS icon tak appear dalam app.',
          role: 'pengguna',
          location: 'Sibu, Sarawak',
          createdAt: DateTime.now().subtract(const Duration(hours: 1, minutes: 40)),
          user: {'id': 10, 'name': 'Fairiz Bin Rahman'},
        ),
        SupportMessage(
          id: 4,
          message: 'Tiket telah di-escalate ke Priority KRITIKAL oleh Staff',
          role: 'sistem',
          createdAt: DateTime.now().subtract(const Duration(hours: 1, minutes: 35)),
          user: null,
        ),
      ];
      
      _isLoading = false;
    });
  }

  @override
  void dispose() {
    _messageController.dispose();
    _scrollController.dispose();
    _pollingTimer?.cancel();
    super.dispose();
  }

  // Load ticket details with messages
  Future<void> _loadTicketDetail() async {
    try {
      final response = await _apiService.getSupportTicketDetail(widget.ticketId);
      
      if (mounted && response['success'] == true) {
        final data = response['data'];
        setState(() {
          _ticket = SupportTicket.fromJson(data);
          _messages = (data['messages'] as List?)
              ?.map((m) => SupportMessage.fromJson(m))
              .toList() ?? [];
          _isLoading = false;
        });
        
        // Scroll to bottom
        WidgetsBinding.instance.addPostFrameCallback((_) {
          if (_scrollController.hasClients) {
            _scrollController.jumpTo(_scrollController.position.maxScrollExtent);
          }
        });
      }
    } catch (e) {
      developer.log('Load ticket error: $e');
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  // Start polling for new messages (every 3 seconds)
  void _startPolling() {
    _pollingTimer = Timer.periodic(const Duration(seconds: 3), (timer) {
      if (mounted && !_isLoading) {
        _refreshMessages();
      }
    });
  }

  // Refresh messages (silent update)
  Future<void> _refreshMessages() async {
    try {
      final response = await _apiService.getSupportMessages(widget.ticketId);
      
      if (mounted && response['success'] == true) {
        final newMessages = (response['data'] as List?)
            ?.map((m) => SupportMessage.fromJson(m))
            .toList() ?? [];
        
        // Only update if message count changed
        if (newMessages.length != _messages.length) {
          setState(() {
            _messages = newMessages;
          });
          
          // Auto-scroll to new message
          Future.delayed(const Duration(milliseconds: 300), () {
            if (_scrollController.hasClients) {
              _scrollController.animateTo(
                _scrollController.position.maxScrollExtent,
                duration: const Duration(milliseconds: 300),
                curve: Curves.easeOut,
              );
            }
          });
        }
      }
    } catch (e) {
      developer.log('Refresh messages error: $e');
    }
  }

  // Send message
  Future<void> _sendMessage() async {
    if (_messageController.text.trim().isEmpty) return;

    setState(() => _isSending = true);

    // Get current GPS location
    double? lat, lng;
    try {
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
        timeLimit: const Duration(seconds: 5),
      );
      lat = position.latitude;
      lng = position.longitude;
    } catch (e) {
      developer.log('GPS error: $e');
      // Continue without GPS if failed
    }

    try {
      final response = await _apiService.sendSupportMessage(
        ticketId: widget.ticketId,
        message: _messageController.text,
        latitude: lat,
        longitude: lng,
        attachments: _attachments.isNotEmpty ? _attachments : null,
      );

      if (mounted && response['success'] == true) {
        _messageController.clear();
        setState(() {
          _attachments.clear();
        });
        
        // Reload messages
        await _loadTicketDetail();
      }
    } catch (e) {
      developer.log('Send message error: $e');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e'), backgroundColor: Colors.red),
        );
      }
    } finally {
      if (mounted) {
        setState(() => _isSending = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: PastelColors.primary,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(widget.ticketNumber, style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w600)),
            if (_ticket != null)
              Text(
                _ticket!.statusLabel ?? _ticket!.status,
                style: const TextStyle(color: Colors.white70, fontSize: 12),
              ),
          ],
        ),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      backgroundColor: const Color(0xFFF5F5F5),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : Column(
              children: [
                // Ticket Info Header
                Container(
                  padding: const EdgeInsets.all(16),
                  color: Colors.white,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        _ticket?.subject ?? '',
                        style: AppTextStyles.bodyLarge.copyWith(fontWeight: FontWeight.w600),
                      ),
                      const SizedBox(height: 8),
                      Row(
                        children: [
                          _buildBadge(_ticket?.priorityLabel ?? '', _getPriorityColor(_ticket?.priority)),
                          const SizedBox(width: 8),
                          _buildBadge(_ticket?.statusLabel ?? '', _getStatusColor(_ticket?.status)),
                        ],
                      ),
                    ],
                  ),
                ),

                // Messages List
                Expanded(
                  child: ListView.builder(
                    controller: _scrollController,
                    padding: const EdgeInsets.all(16),
                    itemCount: _messages.length,
                    itemBuilder: (context, index) {
                      return _buildMessageBubble(_messages[index]);
                    },
                  ),
                ),

                // Reply Input (sticky bottom)
                Container(
                  decoration: BoxDecoration(
                    color: Colors.white,
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.1),
                        blurRadius: 4,
                        offset: const Offset(0, -2),
                      ),
                    ],
                  ),
                  padding: const EdgeInsets.all(12),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      // Attachment preview
                      if (_attachments.isNotEmpty) ...[
                        SizedBox(
                          height: 60,
                          child: ListView.builder(
                            scrollDirection: Axis.horizontal,
                            itemCount: _attachments.length,
                            itemBuilder: (context, index) {
                              return Padding(
                                padding: const EdgeInsets.only(right: 8),
                                child: Stack(
                                  children: [
                                    Container(
                                      width: 60,
                                      height: 60,
                                      decoration: BoxDecoration(
                                        borderRadius: BorderRadius.circular(8),
                                        border: Border.all(color: Colors.grey.shade300),
                                        image: DecorationImage(
                                          image: FileImage(_attachments[index]),
                                          fit: BoxFit.cover,
                                        ),
                                      ),
                                    ),
                                    Positioned(
                                      top: -8,
                                      right: -8,
                                      child: IconButton(
                                        icon: const Icon(Icons.cancel, color: Colors.red, size: 20),
                                        onPressed: () {
                                          setState(() {
                                            _attachments.removeAt(index);
                                          });
                                        },
                                      ),
                                    ),
                                  ],
                                ),
                              );
                            },
                          ),
                        ),
                        const SizedBox(height: 8),
                      ],
                      
                      // Input row
                      Row(
                        children: [
                          IconButton(
                            icon: const Icon(Icons.attach_file),
                            onPressed: () async {
                              final XFile? image = await _picker.pickImage(source: ImageSource.gallery);
                              if (image != null) {
                                setState(() {
                                  _attachments.add(File(image.path));
                                });
                              }
                            },
                            color: Colors.grey,
                          ),
                          Expanded(
                            child: TextField(
                              controller: _messageController,
                              style: AppTextStyles.bodyMedium,
                              maxLines: null,
                              decoration: InputDecoration(
                                hintText: 'Type your message...',
                                filled: true,
                                fillColor: Colors.grey.shade100,
                                border: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(24),
                                  borderSide: BorderSide.none,
                                ),
                                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                              ),
                            ),
                          ),
                          const SizedBox(width: 8),
                          IconButton(
                            icon: _isSending 
                                ? const SizedBox(
                                    width: 20,
                                    height: 20,
                                    child: CircularProgressIndicator(strokeWidth: 2),
                                  )
                                : const Icon(Icons.send),
                            onPressed: _isSending ? null : _sendMessage,
                            color: PastelColors.primary,
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
    );
  }

  Widget _buildMessageBubble(SupportMessage message) {
    final isFromMe = message.isFromUser;
    final isFromAdmin = message.isFromAdmin;
    final isSystem = message.isFromSystem;

    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Avatar
          CircleAvatar(
            radius: 16,
            backgroundColor: isSystem 
                ? Colors.grey.shade300 
                : (isFromAdmin ? Colors.blue.shade100 : Colors.purple.shade100),
            child: Icon(
              isSystem ? Icons.info : (isFromAdmin ? Icons.support_agent : Icons.person),
              size: 16,
              color: isSystem 
                  ? Colors.grey.shade600 
                  : (isFromAdmin ? Colors.blue.shade700 : Colors.purple.shade700),
            ),
          ),
          const SizedBox(width: 12),
          
          // Message content
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Name + timestamp
                Row(
                  children: [
                    Text(
                      message.senderName,
                      style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600),
                    ),
                    const SizedBox(width: 8),
                    Text(
                      _formatTime(message.createdAt),
                      style: TextStyle(fontSize: 10, color: Colors.grey.shade600),
                    ),
                  ],
                ),
                if (message.location != null) ...[
                  const SizedBox(height: 2),
                  Row(
                    children: [
                      Icon(Icons.location_on, size: 10, color: Colors.grey.shade500),
                      const SizedBox(width: 2),
                      Text(
                        message.location!,
                        style: TextStyle(fontSize: 9, color: Colors.grey.shade500),
                      ),
                    ],
                  ),
                ],
                const SizedBox(height: 6),
                
                // Message bubble
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: isFromAdmin 
                        ? Colors.blue.shade50 
                        : (isSystem ? Colors.grey.shade100 : Colors.purple.shade50),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: Colors.grey.shade200),
                  ),
                  child: Text(
                    message.message,
                    style: AppTextStyles.bodyMedium,
                  ),
                ),
                
                // Attachments (if any)
                if (message.attachments.isNotEmpty) ...[
                  const SizedBox(height: 8),
                  ...message.attachments.map((attachment) => Padding(
                    padding: const EdgeInsets.only(bottom: 4),
                    child: InkWell(
                      onTap: () {
                        _showImagePreview(context, attachment);
                      },
                      child: Row(
                        children: [
                          const Icon(Icons.image, size: 14, color: Colors.blue),
                          const SizedBox(width: 4),
                          Expanded(
                            child: Text(
                              attachment.split('/').last,
                              style: const TextStyle(fontSize: 11, color: Colors.blue, decoration: TextDecoration.underline),
                            ),
                          ),
                          const Icon(Icons.visibility, size: 14, color: Colors.blue),
                        ],
                      ),
                    ),
                  )),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBadge(String label, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(4),
        border: Border.all(color: color),
      ),
      child: Text(
        label,
        style: TextStyle(fontSize: 10, color: color, fontWeight: FontWeight.w600),
      ),
    );
  }

  Color _getPriorityColor(String? priority) {
    switch (priority) {
      case 'kritikal':
        return Colors.red;
      case 'tinggi':
        return Colors.orange;
      case 'sederhana':
        return Colors.yellow.shade700;
      case 'rendah':
        return Colors.green;
      default:
        return Colors.grey;
    }
  }

  Color _getStatusColor(String? status) {
    switch (status) {
      case 'baru':
        return Colors.green;
      case 'dalam_proses':
      case 'dijawab':
        return Colors.blue;
      case 'escalated':
        return Colors.red;
      case 'ditutup':
        return Colors.grey;
      default:
        return Colors.grey;
    }
  }

  String _formatTime(DateTime dateTime) {
    final now = DateTime.now();
    final difference = now.difference(dateTime);

    if (difference.inMinutes < 1) {
      return 'Just now';
    } else if (difference.inHours < 1) {
      return '${difference.inMinutes}m ago';
    } else if (difference.inDays < 1) {
      return '${difference.inHours}h ago';
    } else if (difference.inDays < 7) {
      return '${difference.inDays}d ago';
    } else {
      return '${dateTime.day}/${dateTime.month}/${dateTime.year}';
    }
  }

  void _showImagePreview(BuildContext context, String imagePath) {
    // Build full URL
    final imageUrl = imagePath.startsWith('http') 
        ? imagePath 
        : 'http://your-server.com/storage/$imagePath'; // TODO: Use actual base URL

    showDialog(
      context: context,
      builder: (context) => Dialog(
        backgroundColor: Colors.black,
        insetPadding: EdgeInsets.zero,
        child: Stack(
          children: [
            Center(
              child: InteractiveViewer(
                panEnabled: true,
                minScale: 0.5,
                maxScale: 4.0,
                child: Image.network(
                  imageUrl,
                  fit: BoxFit.contain,
                  loadingBuilder: (context, child, loadingProgress) {
                    if (loadingProgress == null) return child;
                    return const Center(
                      child: CircularProgressIndicator(color: Colors.white),
                    );
                  },
                  errorBuilder: (context, error, stackTrace) {
                    return const Center(
                      child: Icon(Icons.error, color: Colors.white, size: 64),
                    );
                  },
                ),
              ),
            ),
            Positioned(
              top: 40,
              right: 16,
              child: IconButton(
                icon: const Icon(Icons.close, color: Colors.white, size: 32),
                onPressed: () => Navigator.pop(context),
              ),
            ),
            Positioned(
              bottom: 40,
              left: 0,
              right: 0,
              child: Center(
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  decoration: BoxDecoration(
                    color: Colors.black54,
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    imagePath.split('/').last,
                    style: const TextStyle(color: Colors.white, fontSize: 12),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

