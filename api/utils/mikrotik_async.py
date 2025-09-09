import ssl
import asyncio
from librouteros import connect


class MikroTikAsyncClient:
    def __init__(self, host, username, password, port=8729, use_ssl=True):
        self.host = host
        self.username = username
        self.password = password
        self.port = port
        self.use_ssl = use_ssl
        self.api = None

    async def connect(self):
        """Async wrapper untuk koneksi blocking librouteros"""
        loop = asyncio.get_event_loop()
        self.api = await loop.run_in_executor(None, self._connect_blocking)

    def _connect_blocking(self):
        """Koneksi sebenarnya ke MikroTik"""
        ssl_wrapper = None
        if self.use_ssl:
            ssl_context = ssl.create_default_context()
            ssl_context.check_hostname = False
            ssl_context.verify_mode = ssl.CERT_NONE
            ssl_wrapper = lambda sock: ssl_context.wrap_socket(sock, server_hostname=self.host)

        return connect(
            username=self.username,
            password=self.password,
            host=self.host,
            port=self.port,
            ssl_wrapper=ssl_wrapper
        )

    async def close(self):
        """Tutup koneksi"""
        if self.api:
            self.api.close()
