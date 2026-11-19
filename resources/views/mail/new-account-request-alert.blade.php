<x-mail::message>
# New Account Request Awaiting PMO Verification

A new personnel account request has been submitted for the **Bureau of Plant Industry - Integrated Performance Monitoring System (BPI-IPMS)** and requires administrative review.

<x-mail::panel>
**Applicant Details**

- **Name:** {{ $applicantName }}
- **Email:** {{ $applicantEmail }}
- **Designation:** {{ $designation }}
- **Requested Role:** {{ $roleLabel }}
- **Operating Unit:** {{ $unitLabel }}
- **Justification:** {{ $justification }}
- **Submitted On:** {{ $submittedAt }}
</x-mail::panel>

Please verify the applicant's credentials and division assignment. You can approve or decline this request directly in the PMO user management portal.

<x-mail::button :url="$reviewUrl">
Review Pending Account Requests
</x-mail::button>

Thank you,<br>
BPI Integrated Performance Monitoring System (BPI-IPMS)
</x-mail::message>
