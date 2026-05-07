package com.absensi.nfc.mobile

import io.flutter.embedding.android.FlutterActivity
import io.flutter.embedding.engine.FlutterEngine
import io.flutter.plugin.common.MethodChannel

class MainActivity : FlutterActivity() {
	private val channelName = "com.absensi.nfc/hce"

	override fun configureFlutterEngine(flutterEngine: FlutterEngine) {
		super.configureFlutterEngine(flutterEngine)

		MethodChannel(flutterEngine.dartExecutor.binaryMessenger, channelName)
			.setMethodCallHandler { call, result ->
				when (call.method) {
					"setToken" -> {
						val token = call.argument<String>("token") ?: ""
						HceService.setToken(token)
						result.success(null)
					}
					"setEnabled" -> {
						val enabled = call.argument<Boolean>("enabled") ?: false
						HceService.setEnabled(enabled)
						result.success(null)
					}
					else -> result.notImplemented()
				}
			}
	}
}
