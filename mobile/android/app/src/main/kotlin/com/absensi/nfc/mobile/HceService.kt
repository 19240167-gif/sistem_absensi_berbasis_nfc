package com.absensi.nfc.mobile

import android.nfc.cardemulation.HostApduService
import android.os.Bundle

class HceService : HostApduService() {
    override fun processCommandApdu(commandApdu: ByteArray, extras: Bundle?): ByteArray {
        if (!isEnabled || token.isBlank()) {
            return hexToBytes(STATUS_FAILED)
        }

        val apdu = toHex(commandApdu)

        return if (apdu.startsWith(SELECT_APDU_HEADER) && apdu.contains(AID)) {
            val payload = token.toByteArray(Charsets.UTF_8)
            payload + hexToBytes(STATUS_SUCCESS)
        } else {
            hexToBytes(STATUS_FAILED)
        }
    }

    override fun onDeactivated(reason: Int) {
        // No-op
    }

    companion object {
        private const val STATUS_SUCCESS = "9000"
        private const val STATUS_FAILED = "6F00"
        private const val SELECT_APDU_HEADER = "00A40400"
        private const val AID = "F0010203040506"

        @Volatile
        private var token: String = ""

        @Volatile
        private var isEnabled: Boolean = false

        fun setToken(value: String) {
            token = value
        }

        fun setEnabled(value: Boolean) {
            isEnabled = value
        }

        private fun toHex(bytes: ByteArray): String {
            val hexChars = "0123456789ABCDEF"
            val result = StringBuilder(bytes.size * 2)
            bytes.forEach { byte ->
                val octet = byte.toInt()
                result.append(hexChars[(octet and 0xF0).ushr(4)])
                result.append(hexChars[octet and 0x0F])
            }
            return result.toString()
        }

        private fun hexToBytes(data: String): ByteArray {
            val clean = data.replace(" ", "")
            val len = clean.length
            val out = ByteArray(len / 2)
            var i = 0
            while (i < len) {
                out[i / 2] = ((clean.substring(i, i + 2)).toInt(16)).toByte()
                i += 2
            }
            return out
        }
    }
}
