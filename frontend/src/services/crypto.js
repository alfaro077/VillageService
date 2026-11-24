import api from './api';

let cachedCryptoKey = null;

/**
 * Konversi kunci publik format PEM ke SPKI ArrayBuffer.
 */
function pemToArrayBuffer(pem) {
  const b64 = pem
    .replace(/-----BEGIN PUBLIC KEY-----/, '')
    .replace(/-----END PUBLIC KEY-----/, '')
    .replace(/\s+/g, '');
  const binary = atob(b64);
  const bytes = new Uint8Array(binary.length);
  for (let i = 0; i < binary.length; i++) {
    bytes[i] = binary.charCodeAt(i);
  }
  return bytes.buffer;
}

/**
 * Konversi ArrayBuffer ke Base64 string.
 */
function arrayBufferToBase64(buffer) {
  let binary = '';
  const bytes = new Uint8Array(buffer);
  for (let i = 0; i < bytes.byteLength; i++) {
    binary += String.fromCharCode(bytes[i]);
  }
  return btoa(binary);
}

/**
 * Ambil dan cache RSA Public Key dari backend server.
 */
export async function fetchServerPublicKey() {
  if (cachedCryptoKey) {
    return cachedCryptoKey;
  }

  const response = await api.get('/security/public-key');
  const pem = response.data.public_key;
  const spkiBuffer = pemToArrayBuffer(pem);

  cachedCryptoKey = await window.crypto.subtle.importKey(
    'spki',
    spkiBuffer,
    { name: 'RSA-OAEP', hash: 'SHA-1' },
    false,
    ['encrypt']
  );

  return cachedCryptoKey;
}

/**
 * Enkripsi hybrid data sensitif di sisi browser (In-Transit) menggunakan Web Crypto API:
 * 1. Generate session key AES-256-GCM
 * 2. Enkripsi data JSON dengan AES-256-GCM
 * 3. Enkripsi session key AES dengan RSA Public Key Server
 */
export async function encryptSensitivePayload(dataObject) {
  try {
    if (!window.crypto || !window.crypto.subtle) {
      console.warn('Web Crypto API tidak tersedia pada browser ini.');
      return dataObject;
    }

    const rsaPublicKey = await fetchServerPublicKey();

    // 1. Generate AES-256-GCM session key
    const aesKey = await window.crypto.subtle.generateKey(
      { name: 'AES-GCM', length: 256 },
      true,
      ['encrypt']
    );

    // 2. Generate IV 12 bytes
    const iv = window.crypto.getRandomValues(new Uint8Array(12));

    // 3. Enkripsi isi data dengan AES-GCM
    const jsonString = JSON.stringify(dataObject);
    const encodedData = new TextEncoder().encode(jsonString);

    const encryptedBuffer = await window.crypto.subtle.encrypt(
      { name: 'AES-GCM', iv: iv },
      aesKey,
      encodedData
    );

    // Pada Web Crypto API, 16-byte terakhir adalah tag otentikasi GCM
    const encryptedBytes = new Uint8Array(encryptedBuffer);
    const tag = encryptedBytes.slice(-16);
    const ciphertext = encryptedBytes.slice(0, -16);

    // 4. Enkripsi AES key dengan RSA Public Key server
    const rawAesKey = await window.crypto.subtle.exportKey('raw', aesKey);
    const encryptedAesKeyBuffer = await window.crypto.subtle.encrypt(
      { name: 'RSA-OAEP' },
      rsaPublicKey,
      rawAesKey
    );

    return {
      _encrypted: true,
      encrypted_key: arrayBufferToBase64(encryptedAesKeyBuffer),
      iv: arrayBufferToBase64(iv),
      data: arrayBufferToBase64(ciphertext),
      tag: arrayBufferToBase64(tag),
      mode: 'aes-256-gcm',
    };
  } catch (error) {
    console.error('Enkripsi In-Transit gagal, menggunakan fallback:', error);
    return dataObject;
  }
}

