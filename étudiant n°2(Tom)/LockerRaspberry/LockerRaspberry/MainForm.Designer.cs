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
            SuspendLayout();
            // 
            // richTextBoxLogs
            // 
            richTextBoxLogs.Location = new Point(260, 128);
            richTextBoxLogs.Name = "richTextBoxLogs";
            richTextBoxLogs.Size = new Size(278, 225);
            richTextBoxLogs.TabIndex = 0;
            richTextBoxLogs.Text = "";
            // 
            // btnValiderCode
            // 
            btnValiderCode.Location = new Point(260, 368);
            btnValiderCode.Name = "btnValiderCode";
            btnValiderCode.Size = new Size(75, 23);
            btnValiderCode.TabIndex = 1;
            btnValiderCode.Text = "Valider";
            btnValiderCode.UseVisualStyleBackColor = true;
            // 
            // btnFermerCasier
            // 
            btnFermerCasier.Location = new Point(449, 368);
            btnFermerCasier.Name = "btnFermerCasier";
            btnFermerCasier.Size = new Size(75, 23);
            btnFermerCasier.TabIndex = 2;
            btnFermerCasier.Text = "Fermer";
            btnFermerCasier.UseVisualStyleBackColor = true;
            // 
            // txtCode
            // 
            txtCode.Location = new Point(48, 38);
            txtCode.Name = "txtCode";
            txtCode.Size = new Size(100, 23);
            txtCode.TabIndex = 3;
            // 
            // MainForm
            // 
            AutoScaleDimensions = new SizeF(7F, 15F);
            AutoScaleMode = AutoScaleMode.Font;
            ClientSize = new Size(800, 450);
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
    }
}
