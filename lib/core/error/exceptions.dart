class ServerException implements Exception {
  final String message;
  
  ServerException({required this.message});
}

class ConnectionException implements Exception {
  final String message;
  
  ConnectionException({required this.message});
}

class AuthenticationException implements Exception {
  final String message;
  
  AuthenticationException({required this.message});
}

class CacheException implements Exception {
  final String message;
  
  CacheException({required this.message});
}

class ValidationException implements Exception {
  final String message;
  
  ValidationException({required this.message});
} 