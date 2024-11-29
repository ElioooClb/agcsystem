<body style="font-family: Arial, sans-serif; margin: 0; padding: 0;">
    <header>
        <table style="width: 100%;" background="{{ $message->embed($backgroundImage) }}">
        {{-- <table style="width: 100%;"> --}}
            <tr>
                <td align="center">
                    <h1 style="margin-bottom: 0px;">Notification automatique</h1>
                    <h2 style="color: #868686; margin-top: 0px; font-size: 1rem;">Merci de ne pas répondre</h2>
                </td>
            </tr>
        </table>
    </header>
    <table style="width: 100%;">
        <tr>
            <td align="center">
                <table class="container">
                    <tr>
                        <td style="width: 250px; background-color: #f4f4f4; padding: 20px;">
                            <h2 style="margin-bottom: 0.3rem; padding-left: 0.5rem;">Informations</h2>
                            <h3 style="margin-top: 0; font-size: 1rem; padding-left: 0.5rem;">Chantier : {{ $data['title'] }}</h3>
                            <table style="border-collapse: collapse;">
                                <tr>
                                    <td><u>idAff</u> :&nbsp;&nbsp;</td>
                                    <td><strong>{{ $data['idaff'] }}</strong></td>
                                </tr>
                                <tr>
                                    <td><u>Numéro de facture</u> :&nbsp;&nbsp;</td>
                                    <td><strong>{{ $data['invoiceNumber'] ?? 'Aucun antécédent' }}</strong></td>
                                </tr>
                            </table>
                        </td>
                        <td style="padding: 20px; text-align: center;">
                            <h2>Action requise</h2>
                            <p>{{ $data['isAccountant'] ? 'Une demande a été faite' : 'Une facture a été enregistrée' }} pour la facturation <strong>{{ $data['isFinal'] ? 'finale' : 'partielle' }}</strong> du chantier <strong>{{ $data['title'] }}</strong></p>
                            <a href="https://gtemptr6.fr/" style="padding-top: 1.5rem;">www.gtemptr6.fr</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table style="width: 100%;" background="{{ $message->embed($backgroundImage) }}">
    {{-- <table style="width: 100%;"> --}}
        <tr>
            <td align="center">
                <table style="padding: 20px; text-align: center; width: 80%;">
                    <tr>
                        <td>
                            <div>
                                <img src="cid:logo.png" style="width: 200px;" width="200">
                                <p>&copy; 2024 Tous droits réservés</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
