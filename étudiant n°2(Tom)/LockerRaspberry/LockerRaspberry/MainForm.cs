using System;
using System.Windows.Forms;
using LockerRaspberry.Controllers;
using LockerRaspberry.Models;
using LockerRaspberry.Services;

namespace LockerRaspberry
{
    public partial class MainForm : Form
    {
        private readonly ApiService apiService;
        private readonly LockerController lockerController;

        private int casierActuel = -1;

        public MainForm()
        {
            InitializeComponent();

            apiService = new ApiService();
            lockerController = new LockerController();

            AjouterLog("Application Locker Raspberry démarrée.");
        }

        private async void btnTesterApi_Click(object sender, EventArgs e)
        {
            AjouterLog("Test de connexion à l'API...");

            string resultat = await apiService.TesterApiAsync();

            AjouterLog(resultat);
        }

        private async void btnValiderCode_Click(object sender, EventArgs e)
        {
            string numeroColis = txtCode.Text.Trim();

            if (numeroColis == "")
            {
                AjouterLog("Veuillez saisir un numéro de colis.");
                return;
            }

            AjouterLog($"Recherche du colis {numeroColis}...");

            Colis? colis = await apiService.RechercherColisAsync(numeroColis);

            if (colis == null)
            {
                AjouterLog("Colis introuvable ou erreur API.");
                return;
            }

            casierActuel = RecupererCasier(colis);

            AjouterLog($"Colis trouvé : {colis.NumColis}");
            AjouterLog($"Casier associé : {casierActuel}");

            lockerController.OuvrirCasier(casierActuel);

            AjouterLog($"Casier {casierActuel} ouvert.");
        }

        private void btnFermerCasier_Click(object sender, EventArgs e)
        {
            if (casierActuel == -1)
            {
                AjouterLog("Aucun casier ouvert.");
                return;
            }

            lockerController.FermerCasier(casierActuel);

            AjouterLog($"Casier {casierActuel} fermé.");

            casierActuel = -1;
        }

        private int RecupererCasier(Colis colis)
        {
            if (colis.CasierIdCasier.HasValue)
            {
                return colis.CasierIdCasier.Value;
            }

            AjouterLog("Aucun casier associé par l'API. Casier 1 utilisé pour test.");
            return 1;
        }

        private void AjouterLog(string message)
        {
            richTextBoxLogs.AppendText($"[{DateTime.Now:HH:mm:ss}] {message}\n");
        }
    }
}