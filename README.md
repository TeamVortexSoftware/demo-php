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

The demo includes two pre-configured users with the new simplified structure:

| Email | Password | Auto-Join Admin |
|-------|----------|-----------------|
| admin@example.com | password123 | Yes |
| user@example.com | userpass | No |

## JWT Format

This demo uses Vortex's **new JWT format with user array**:

```php
// Create a user array with admin scopes
$user = [
    'id' => 'user-123',
    'email' => 'user@example.com',
    'adminScopes' => ['autoJoin']  // Optional: grants admin privileges
];

// Generate JWT
$jwt = $vortex->generateJwt($user);

// Or with extra properties
$jwt = $vortex->generateJwt($user, [
    'role' => 'admin',
    'department' => 'Engineering'
]);
```

The JWT payload includes:
- `userId`: User's unique ID
- `userEmail`: User's email address
- `userIsAutoJoinAdmin`: Set to `true` when `adminScopes` contains `'autoJoin'`
- Any additional properties from the second parameter

This replaces the legacy format with identifiers, groups, and role fields.

The demo automatically uses the new format when generating JWTs for authenticated users (see `handleGenerateJWT()` in `public/index.php`).

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
