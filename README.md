# Vortex PHP SDK Demo

A demo application showcasing the Vortex PHP SDK integration.

## Features

- 🔐 **Authentication System**: Session-based auth with JWT tokens
- ⚡ **Vortex Integration**: Full Vortex API integration for invitation management
- 🎯 **JWT Generation**: Generate Vortex JWTs for authenticated users
- 📧 **Invitation Management**: Get, accept, revoke, and reinvite functionality
- 🌐 **Interactive Frontend**: Complete HTML interface to test all features

## Prerequisites

- PHP 8.0 or later
- Composer (optional, for autoloading)
- The Vortex PHP SDK (automatically linked via workspace)

## Running the Demo

1. Navigate to the demo directory:
   ```bash
   cd apps/demo-php
   ```

2. Set your Vortex API key (optional - defaults to demo key):
   ```bash
   export VORTEX_API_KEY=your-api-key-here
   ```

3. Run the PHP built-in server:
   ```bash
   php -S localhost:8000 -t public
   ```

4. Open your browser and visit: `http://localhost:8000`

## Demo Users

The demo includes two pre-configured users:

| Email | Password | Role | Groups |
|-------|----------|------|--------|
| alice@example.com | password123 | admin | Main Workspace, Engineering Team |
| bob@example.com | password123 | member | Main Workspace |

## API Endpoints

- `POST /api/auth/login` - Login with email/password
- `POST /api/auth/logout` - Logout current user
- `GET /api/auth/me` - Get current user info
- `GET /api/auth/users` - Get demo users list
- `POST /api/vortex/jwt` - Generate Vortex JWT
- `GET /api/vortex/invitations` - Get invitations by target
- `GET /api/vortex/invitations/{id}` - Get specific invitation
- `DELETE /api/vortex/invitations/{id}` - Revoke invitation

## License

MIT
