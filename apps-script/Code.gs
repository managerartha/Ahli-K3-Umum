/**
 * Handler HTTP POST untuk menerima data pendaftaran dari Landing Page HTML
 * dan mencatatnya ke Google Spreadsheet secara otomatis.
 */

// Nama tab (sheet) tujuan. Jika tab dengan nama ini tidak ada,
// data ditulis ke tab pertama di spreadsheet.
var SHEET_NAME = "Pendaftaran";

function doPost(e) {
  var lock = LockService.getScriptLock();
  // Menunggu antrean maksimal 10 detik agar penulisan data tidak bentrok
  if (!lock.tryLock(10000)) {
    return jsonOutput({ result: "error", message: "Server sibuk, silakan coba lagi" });
  }

  try {
    var ss = SpreadsheetApp.getActiveSpreadsheet();
    var sheet = ss.getSheetByName(SHEET_NAME) || ss.getSheets()[0];
    var data = JSON.parse(e.postData.contents);

    // Buat header kolom otomatis jika sheet masih kosong
    if (sheet.getLastRow() === 0) {
      sheet.appendRow([
        "Waktu Submit",
        "Nama Lengkap",
        "Nomor WhatsApp",
        "Email",
        "Tujuan Mengikuti",
        "Skema Pelatihan"
      ]);

      // Format header agar rapi
      var headerRange = sheet.getRange(1, 1, 1, 6);
      headerRange.setFontWeight("bold");
      headerRange.setBackground("#070D42");
      headerRange.setFontColor("#FFFFFF");
    }

    var timestamp = data.timestamp || new Date().toLocaleString("id-ID", { timeZone: "Asia/Jakarta" }) + " WIB";

    // Tambahkan baris data baru ke sheet
    sheet.appendRow([
      safeCell(timestamp),
      safeCell(data.name),
      "'" + (data.phone || "-"), // Tanda kutip agar angka 0 di depan nomor WhatsApp tidak hilang
      safeCell(data.email),
      safeCell(data.goal),
      safeCell(data.scheme)
    ]);

    return jsonOutput({ result: "success", message: "Data berhasil dicatat" });

  } catch (error) {
    return jsonOutput({ result: "error", message: error.toString() });

  } finally {
    lock.releaseLock();
  }
}

/**
 * Handler GET untuk memastikan Web App aktif saat diuji di browser
 */
function doGet(e) {
  return ContentService.createTextOutput("Webhook Artha Safety Aktif dan Siap Menerima Data.")
    .setMimeType(ContentService.MimeType.TEXT);
}

/**
 * Mencegah isian form dibaca sebagai rumus oleh Spreadsheet
 * (misal nama diawali "=", "+", "-", "@"), dan membatasi panjang isian.
 */
function safeCell(value) {
  var s = String(value == null || value === "" ? "-" : value).slice(0, 500);
  return /^[=+\-@]/.test(s) ? "'" + s : s;
}

function jsonOutput(obj) {
  return ContentService.createTextOutput(JSON.stringify(obj))
    .setMimeType(ContentService.MimeType.JSON);
}
