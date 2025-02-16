import 'package:flutter/material.dart';
import 'package:qr_flutter/qr_flutter.dart';  // Importez qr_flutter

class QrCodePage extends StatelessWidget {
  final String billetId;

  QrCodePage({required this.billetId});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Votre QR Code'),
      ),
      body: Center(
        child: QrImage(
          data: billetId,  // Utilisez ici les données à encoder dans le QR code
          size: 200.0,      // Taille du QR code
          backgroundColor: Colors.white,  // Couleur de fond (optionnel)
        ),
      ),
    );
  }
}
