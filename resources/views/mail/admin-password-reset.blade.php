<x-mail::message>
# Account Password Reset Notice

Hello {{ $user->name }},

Your password for the **Bureau of Plant Industry - Integrated Performance Monitoring System (BPI-IPMS)** has been reset by the PMO Administrator.

<x-mail::panel>
**Account Details**

- **Account Email:** {{ $user->email }}
- **Reset Date:** {{ $resetAt }}
</x-mail::panel>

Please obtain your temporary password directly from the PMO Administrator, sign in using the button below, and update your password from your profile immediately.

<x-mail::button :url="$loginUrl">
Sign In to BPI-IPMS
</x-mail::button>

If you did not request this password reset, please contact the PMO immediately.

Thank you,<br>
BPI Planning and Management Office (PMO)
</x-mail::message>
