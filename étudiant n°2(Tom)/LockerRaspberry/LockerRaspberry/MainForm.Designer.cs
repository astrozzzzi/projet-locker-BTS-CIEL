namespace LockerRaspberry
{
    partial class MainForm
    {
        /// <summary>
        ///  Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        ///  Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        ///  Required method for Designer support - do not modify
        ///  the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            richTextBoxLogs = new RichTextBox();
            btnValiderCode = new Button();
            btnFermerCasier = new Button();
            txtCode = new TextBox();
            labelConnexion = new Label();
            label3 = new Label();
            SuspendLayout();
            // 
            // richTextBoxLogs
            // 
            richTextBoxLogs.Location = new Point(30, 324);
            richTextBoxLogs.Name = "richTextBoxLogs";
            richTextBoxLogs.Size = new Size(735, 78);
            richTextBoxLogs.TabIndex = 0;
            richTextBoxLogs.Text = "";
            // 
            // btnValiderCode
            // 
            btnValiderCode.Location = new Point(342, 147);
            btnValiderCode.Name = "btnValiderCode";
            btnValiderCode.Size = new Size(75, 23);
            btnValiderCode.TabIndex = 1;
            btnValiderCode.Text = "Valider";
            btnValiderCode.UseVisualStyleBackColor = true;
            // 
            // btnFermerCasier
            // 
            btnFermerCasier.Location = new Point(342, 195);
            btnFermerCasier.Name = "btnFermerCasier";
            btnFermerCasier.Size = new Size(75, 23);
            btnFermerCasier.TabIndex = 2;
            btnFermerCasier.Text = "Fermer";
            btnFermerCasier.UseVisualStyleBackColor = true;
            // 
            // txtCode
            // 
            txtCode.Location = new Point(314, 104);
            txtCode.Name = "txtCode";
            txtCode.Size = new Size(144, 23);
            txtCode.TabIndex = 3;
            // 
            // labelConnexion
            // 
            labelConnexion.AutoSize = true;
            labelConnexion.Location = new Point(349, 20);
            labelConnexion.Name = "labelConnexion";
            labelConnexion.Size = new Size(64, 15);
            labelConnexion.TabIndex = 4;
            labelConnexion.Text = "Connexion";
            // 
            // label3
            // 
            label3.AutoSize = true;
            label3.Location = new Point(349, 86);
            label3.Name = "label3";
            label3.Size = new Size(68, 15);
            label3.TabIndex = 7;
            label3.Text = "Code colis :";
            // 
            // MainForm
            // 
            AutoScaleDimensions = new SizeF(7F, 15F);
            AutoScaleMode = AutoScaleMode.Font;
            ClientSize = new Size(800, 427);
            Controls.Add(label3);
            Controls.Add(labelConnexion);
            Controls.Add(txtCode);
            Controls.Add(btnFermerCasier);
            Controls.Add(btnValiderCode);
            Controls.Add(richTextBoxLogs);
            Name = "MainForm";
            Text = "Form1";
            ResumeLayout(false);
            PerformLayout();
        }

        #endregion

        private RichTextBox richTextBoxLogs;
        private Button btnValiderCode;
        private Button btnFermerCasier;
        private TextBox txtCode;
        private Label labelConnexion;
        private Label label3;
    }
}
