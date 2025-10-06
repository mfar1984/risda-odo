class SupportTicket {
  final int id;
  final String ticketNumber;
  final String subject;
  final String category;
  final String priority;
  final String status;
  final String? statusLabel;
  final String? priorityLabel;
  final int messageCount;
  final DateTime createdAt;
  final DateTime? lastReplyAt;
  final Map<String, dynamic>? creator;
  final Map<String, dynamic>? assignedTo;

  SupportTicket({
    required this.id,
    required this.ticketNumber,
    required this.subject,
    required this.category,
    required this.priority,
    required this.status,
    this.statusLabel,
    this.priorityLabel,
    required this.messageCount,
    required this.createdAt,
    this.lastReplyAt,
    this.creator,
    this.assignedTo,
  });

  factory SupportTicket.fromJson(Map<String, dynamic> json) {
    return SupportTicket(
      id: json['id'] ?? 0,
      ticketNumber: json['ticket_number'] ?? '',
      subject: json['subject'] ?? '',
      category: json['category'] ?? '',
      priority: json['priority'] ?? '',
      status: json['status'] ?? '',
      statusLabel: json['status_label'],
      priorityLabel: json['priority_label'],
      messageCount: json['message_count'] ?? 0,
      createdAt: json['created_at'] != null 
          ? DateTime.parse(json['created_at']) 
          : DateTime.now(),
      lastReplyAt: json['last_reply_at'] != null 
          ? DateTime.parse(json['last_reply_at']) 
          : null,
      creator: json['creator'],
      assignedTo: json['assigned_to'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'ticket_number': ticketNumber,
      'subject': subject,
      'category': category,
      'priority': priority,
      'status': status,
      'message_count': messageCount,
    };
  }
}

class SupportMessage {
  final int id;
  final String message;
  final String role;
  final List<String> attachments;
  final String? ipAddress;
  final String? location;
  final DateTime createdAt;
  final Map<String, dynamic>? user;

  SupportMessage({
    required this.id,
    required this.message,
    required this.role,
    this.attachments = const [],
    this.ipAddress,
    this.location,
    required this.createdAt,
    this.user,
  });

  factory SupportMessage.fromJson(Map<String, dynamic> json) {
    return SupportMessage(
      id: json['id'] ?? 0,
      message: json['message'] ?? '',
      role: json['role'] ?? 'pengguna',
      attachments: json['attachments'] != null 
          ? List<String>.from(json['attachments']) 
          : [],
      ipAddress: json['ip_address'],
      location: json['location'],
      createdAt: json['created_at'] != null 
          ? DateTime.parse(json['created_at']) 
          : DateTime.now(),
      user: json['user'],
    );
  }

  bool get isFromUser => role == 'pengguna';
  bool get isFromAdmin => role == 'admin';
  bool get isFromSystem => role == 'sistem';
  
  String get senderName => user?['name'] ?? 'Sistem';
}

