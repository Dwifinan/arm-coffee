# English Presentation Guide for Arm Coffee System

## 1. Structure of the Presentation (Struktur Presentasi)

A good technical presentation usually follows this flow:

1.  **Introduction**: Who you are and what the project is.
2.  **Problem Statement**: Why did you build this? (e.g., manual counting, food waste).
3.  **The Solution**: How your system solves it.
4.  **Key Features (Demo)**: Show the features live.
5.  **Technical Highlights**: Mention Laravel, MySQL, FEFO algorithm.
6.  **Conclusion**: Summary of impact.

---

## 2. Key Vocabulary (Kosakata Penting)

| Indonesian | English Terminology | Context |
| :--- | :--- | :--- |
| Bahan Baku | **Raw Ingredients** / **Inventory** | "We track the raw ingredients..." |
| Tanggal Kadaluarsa | **Expiration Date** / **Expiry Date** | "The system monitors expiration dates." |
| Stok Menipis | **Low Stock** / **Running Low** | "The dashboard alerts us when stock is low." |
| Masuk Pertama, Kadaluarsa Pertama Keluar | **FEFO (First Expired, First Out)** | "We use the FEFO method to minimize waste." |
| Mengurangi Stok | **Deduct Stock** / **Consume Stock** | "Production automatically deducts the stock." |
| Satuan (Gram, Kg) | **Units of Measurement** | "The system handles unit conversion automatically." |
| Resep / Menu | **Recipe** / **Menu Item** | "Each menu item has a recipe linked to ingredients." |
| Peringatan / Notifikasi | **Alert** / **Notification** | "Users get an alert 7 days before expiration." |
| Membuang (stok rusak) | **Dispose** / **Write-off** | "We can dispose of spoiled ingredients." |
| Pengadaan / Belanja | **Procurement** / **Purchasing** | "The system generates a purchasing list." |

---

## 3. Example Script (Contoh Naskah)

### **Part 1: Introduction**
"Good morning everyone. My name is [Your Name]. Today, I would like to present my thesis project titled **'Arm Coffee Inventory System'**."

"This is a web-based application designed to help coffee shops manage their inventory efficiently."

### **Part 2: The Problem**
"The main problem we identified is **Food Waste** and **Stock Discrepancy**.
Coffee shops often struggle with tracking ingredients that are about to expire. Without a system, they might unknowingly serve expired milk or throw away unused beans."

### **Part 3: The Solution & Technical Approach**
"To solve this, I built an inventory system using **Laravel** that implements the **FEFO (First Expired, First Out)** logic.
This means the system prioritizes using ingredients that are closest to their expiration date, ensuring freshness and reducing waste."

### **Part 4: The Demo (Walkthrough)**

**[Dashboard]**
"Let's look at the **Dashboard**. Here, the manager gets an immediate overview.
You can see these **Alert Cards**. This one shows items that are 'Expiring Soon' (within 7 days). This helps the staff take action immediately."

**[Ingredient Management - Bahan]**
"Now, let's go to the **Ingredients Page (Bahan)**.
Here, we can add new stock. Notice that we must input the **Expiration Date** for every batch. This is crucial for the FEFO calculation."

**[Production - Produksi]**
"Move to the **Production Page**. When a barista makes a coffee, they record it here.
For example, if I produce 5 cups of 'Latte', the system automatically calculates the required milk and beans based on the **Recipe**.
It will then **deduct the stock** from the batch that expires fastests (FEFO). It also handles **Unit Conversion**, for example, converting Milliliters (ml) to Liters (L) automatically."

**[Procurement - Belanja]**
"Finally, the **Purchasing Module**. Based on current stock levels, the system can suggest a shopping list for the next procurement cycle."

### **Part 5: Conclusion**
"In conclusion, the Arm Coffee System not only digitizes manual recording but actively helps in **Cost Reduction** by minimizing waste through intelligent tracking. Thank you."

---

## 4. Q&A Preparation (Persiapan Tanya Jawab)

**Q: Why did you choose FEFO over FIFO?**
*A: "Because in the food and beverage industry, freshness is the priority. FIFO (First In First Out) only cares about arrival time, but a newer batch might expire sooner than an older one. FEFO is safer for food safety."*

**Q: How do you handle unit conversion?**
*A: "I store a base unit for each ingredient in the database. When a recipe uses a different unit (e.g., using grams when stock is in kg), the system applies a conversion factor logic before deducting the stock."*
