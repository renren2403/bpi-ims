<x-mail::message>
# Account Approved

Hello {{ $user->name }},

Your access request for the **Bureau of Plant Industry - Integrated Performance Monitoring System (BPI-IPMS)** has been **approved and activated** by the PMO Administrator.

<x-mail::panel>
**Account Details**

- **Name:** {{ $user->name }}
- **Email:** {{ $user->email }}
- **Assigned Role:** {{ $roleLabel }}
- **Operating Unit:** {{ $unitLabel }}
- **Status:** Approved & Active
@if ($user->approval_remarks)
- **Remarks:** {{ $user->approval_remarks }}
@endif
</x-mail::panel>

You can now sign in using your registered email and password.

<x-mail::button :url="$loginUrl">
Sign In to BPI-IPMS
</x-mail::button>

Thank you,<br>
BPI Planning and Management Office (PMO)
</x-mail::message>
