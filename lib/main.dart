import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:sfm_app/core/di/injection_container.dart' as di;
import 'package:sfm_app/features/auth/presentation/bloc/auth_bloc.dart';
import 'package:sfm_app/features/auth/presentation/bloc/auth_event.dart';
import 'package:sfm_app/features/auth/presentation/bloc/auth_state.dart';
import 'package:sfm_app/features/auth/presentation/pages/home_page.dart';
import 'package:sfm_app/features/auth/presentation/pages/login_page.dart';
import 'package:sfm_app/features/evaluaciones/presentation/bloc/evaluaciones_bloc.dart';
import 'package:sfm_app/features/fincas/presentation/bloc/finca_bloc.dart';

// --- Colores de la App ---
const Color primaryGreen = Color(0xFF2E7D32); // Un verde un poco más oscuro
const Color lightGreen = Color(0xFFC8E6C9);
const Color accentColor = Color(0xFFFF9800); // Naranja para acentos (como badges)
const Color backgroundColor = Colors.white; // Cambiar backgroundColor a blanco
const Color cardBackgroundColor = Colors.white;
const Color textColorPrimary = Color(0xFF212121);
const Color textColorSecondary = Color(0xFF757575);
const Color subtleBorderColor = Color(0xFFE0E0E0); // Un gris claro para bordes

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Inicializar el contenedor de dependencias
  await di.init();
  
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiBlocProvider(
      providers: [
        BlocProvider(
          create: (context) => di.sl<AuthBloc>()..add(const CheckAuthStatusEvent()),
        ),
        BlocProvider(
          create: (context) => di.sl<EvaluacionesBloc>(),
        ),
        BlocProvider(
          create: (context) => di.sl<FincaBloc>(),
        ),
      ],
      child: MaterialApp(
        title: 'Agrojurado SFM',
        debugShowCheckedModeBanner: false,
        theme: _buildThemeData(), // Aplicar el tema
        home: const AuthWrapper(),
      ),
    );
  }

  // --- Definición del Tema ---
  ThemeData _buildThemeData() {
    return ThemeData(
      useMaterial3: true,
      colorScheme: ColorScheme.fromSeed(
        seedColor: primaryGreen,
        primary: primaryGreen,
        // Puedes ajustar otros colores del scheme si es necesario
        // secondary: accentColor,
        background: backgroundColor, // Fondo blanco
        surface: cardBackgroundColor, // Superficies blancas (cards, dialogs)
        onPrimary: Colors.white,
        onBackground: textColorPrimary,
        onSurface: textColorPrimary,
      ),
      scaffoldBackgroundColor: backgroundColor, // Fondo general blanco
      appBarTheme: const AppBarTheme(
        backgroundColor: cardBackgroundColor, 
        foregroundColor: primaryGreen, 
        elevation: 0.6, // Sombra un poco más notable
        surfaceTintColor: Colors.transparent,
        titleTextStyle: TextStyle(
          color: primaryGreen,
          fontSize: 18,
          fontWeight: FontWeight.w600,
        ),
        iconTheme: IconThemeData(color: primaryGreen, size: 24),
        actionsIconTheme: IconThemeData(color: primaryGreen, size: 24),
      ),
      cardTheme: CardTheme(
        elevation: 1.5, // Un poco más de elevación para destacar en fondo blanco
        color: cardBackgroundColor,
        surfaceTintColor: Colors.transparent, 
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12.0),
          // Borde sutil para mejor contraste con fondo blanco
          side: BorderSide(color: subtleBorderColor, width: 0.8), 
        ),
      ),
      expansionTileTheme: ExpansionTileThemeData(
        tilePadding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 4.0),
        childrenPadding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0).copyWith(top: 0),
        iconColor: primaryGreen,
        collapsedIconColor: Colors.grey[600],
        shape: const Border(), // Quitar borde por defecto
        collapsedShape: const Border(), // Quitar borde por defecto
      ),
      listTileTheme: ListTileThemeData(
        iconColor: textColorSecondary,
        titleTextStyle: const TextStyle(
          color: textColorPrimary,
          fontSize: 15,
          fontWeight: FontWeight.w500
        ),
        subtitleTextStyle: TextStyle(
          color: textColorSecondary,
          fontSize: 13,
        ),
      ),
      textTheme: const TextTheme(
        titleLarge: TextStyle(color: textColorPrimary, fontWeight: FontWeight.w600, fontSize: 22), 
        titleMedium: TextStyle(color: textColorPrimary, fontWeight: FontWeight.w500, fontSize: 16), // Para títulos de sección
        bodyLarge: TextStyle(color: textColorPrimary, fontSize: 15),
        bodyMedium: TextStyle(color: textColorSecondary, fontSize: 13), // Texto secundario
        labelLarge: TextStyle(color: Colors.white, fontWeight: FontWeight.w500, fontSize: 14), // Para botones
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: primaryGreen,
          foregroundColor: Colors.white,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8.0),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
        ),
      ),
      // Puedes añadir más personalizaciones (InputDecorationTheme, etc.)
    );
  }
}

class AuthWrapper extends StatelessWidget {
  const AuthWrapper({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<AuthBloc, AuthState>(
      builder: (context, state) {
        switch (state.status) {
          case AuthStatus.loading:
            return const Scaffold(
              body: Center(
                child: CircularProgressIndicator(color: primaryGreen), // Usar color primario
              ),
            );
          case AuthStatus.authenticated:
            return const HomePage();
          case AuthStatus.unauthenticated:
          case AuthStatus.error:
          case AuthStatus.initial:
          default:
            return const LoginPage();
        }
      },
    );
  }
}
