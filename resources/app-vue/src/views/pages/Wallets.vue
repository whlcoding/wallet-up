<script setup>
import { computed, onMounted, ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import walletService from '@/service/WalletService';
import { useWalletStore } from '@/stores/walletStore';

const toast = useToast();
const walletStore = useWalletStore();

const loading = ref(false);
const walletDialog = ref(false);
const deleteDialog = ref(false);
const submitted = ref(false);
const editingWalletId = ref(null);
const walletForm = ref({ name: '' });
const walletToDelete = ref(null);

const wallets = computed(() => walletStore.wallets || []);
const selectedWalletId = computed(() => walletStore.selectedWallet?.id ?? null);

onMounted(() => {
    fetchWallets();
});

// Helper to generate deterministic mock info per wallet
const mockPool = {
    descriptions: [
        'Primary pocket for daily expenses',
        'Savings for the next big thing',
        'Travel fund - dreams & plans',
        'Emergency stash - keep it safe',
        'Investment account snapshot',
        'Shared family wallet',
    ],
    colors: ['#4f46e5', '#059669', '#d97706', '#b91c1c', '#0ea5e9', '#7c3aed']
};

function pickDeterministic(arr, seed) {
    if (!arr.length) return null;
    const idx = Math.abs(Number(seed)) % arr.length;
    return arr[idx];
}

const displayWallets = computed(() => {
    return (wallets.value || []).map((w) => {
        const description = w.description || pickDeterministic(mockPool.descriptions, w.id);
        const color = w.color || pickDeterministic(mockPool.colors, w.id);
        const transactionsCount = w.transactionsCount ?? ((Number(w.id) * 7) % 48 + 1);
        const daysAgo = (Number(w.id) % 30) + 1;
        const lastActivity = w.lastActivity || new Date(Date.now() - daysAgo * 24 * 60 * 60 * 1000).toISOString();
        const initials = (w.name || 'W').split(' ').map(s => s[0]).slice(0,2).join('').toUpperCase();
        return { ...w, description, color, transactionsCount, lastActivity, initials };
    });
});

const fetchWallets = async () => {
    loading.value = true;
    try {
        const response = await walletService.getAll();
        const data = response.data?.data || [];
        walletStore.setWallets(data);

        if (!walletStore.selectedWallet && data.length) {
            walletStore.setSelectedWallet(data[0]);
        } else if (walletStore.selectedWallet) {
            const updatedSelection = data.find((wallet) => wallet.id === walletStore.selectedWallet.id);
            walletStore.setSelectedWallet(updatedSelection || data[0] || null);
        }
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load wallets', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const openNew = () => {
    walletForm.value = { name: '' };
    submitted.value = false;
    editingWalletId.value = null;
    walletDialog.value = true;
};

const openEdit = (wallet) => {
    walletForm.value = { name: wallet.name };
    editingWalletId.value = wallet.id;
    submitted.value = false;
    walletDialog.value = true;
};

const hideDialog = () => {
    walletDialog.value = false;
};

const saveWallet = async () => {
    submitted.value = true;
    if (!walletForm.value.name.trim()) {
        return;
    }

    try {
        if (editingWalletId.value) {
            const response = await walletService.update(editingWalletId.value, { name: walletForm.value.name });
            const updated = response.data?.data;
            if (updated) {
                walletStore.updateWallet(updated);
            }
            toast.add({ severity: 'success', summary: 'Updated', detail: 'Wallet updated successfully', life: 3000 });
        } else {
            const response = await walletService.create({ name: walletForm.value.name });
            const created = response.data?.data;
            if (created) {
                walletStore.addWallet(created);
                walletStore.setSelectedWallet(created);
            }
            toast.add({ severity: 'success', summary: 'Created', detail: 'Wallet created successfully', life: 3000 });
        }
        walletDialog.value = false;
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to save wallet', life: 3000 });
    }
};

const confirmDelete = (wallet) => {
    walletToDelete.value = wallet;
    deleteDialog.value = true;
};

const deleteWallet = async () => {
    if (!walletToDelete.value) return;

    try {
        await walletService.delete(walletToDelete.value.id);
        walletStore.removeWallet(walletToDelete.value.id);
        toast.add({ severity: 'success', summary: 'Deleted', detail: 'Wallet deleted successfully', life: 3000 });
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete wallet', life: 3000 });
    } finally {
        deleteDialog.value = false;
        walletToDelete.value = null;
    }
};

const setActiveWallet = (wallet) => {
    walletStore.setSelectedWallet(wallet);
    toast.add({ severity: 'success', summary: 'Updated', detail: 'Active wallet updated', life: 3000 });
};

const formatCurrency = (value) => {
    return value?.toLocaleString('en-US', { style: 'currency', currency: 'USD' }) ?? '$0.00';
};

const formatDate = (iso) => {
    try {
        const d = new Date(iso);
        return d.toLocaleDateString();
    } catch (e) {
        return iso;
    }
};
</script>

<template>
    <div class="grid wallets">
        <div class="col-12">
            <div class="card">
                <div class="flex flex-column md:flex-row md:align-items-center md:justify-content-between mb-3">
                    <div>
                        <h5 class="m-0">Wallets</h5>
                        <span class="text-500">Manage your wallets with an improved visual layout.</span>
                    </div>
                    <div class="flex gap-2">
                        <Button label="New Wallet" icon="pi pi-plus" class="mt-3 md:mt-0" @click="openNew" />
                    </div>
                </div>

                <div class="wallet-grid">
                    <div v-if="loading" class="loading-overlay">Loading wallets...</div>

                    <div v-for="wallet in displayWallets" :key="wallet.id" class="wallet-card" :class="{ active: selectedWalletId === wallet.id }">
                        <div class="wallet-header">
                            <div class="avatar" :style="{ background: wallet.color }">{{ wallet.initials }}</div>
                            <div class="wallet-title">
                                <div class="name-row">
                                    <span class="wallet-name">{{ wallet.name }}</span>
                                    <Tag v-if="selectedWalletId === wallet.id" value="Active" severity="success" class="ml-2" />
                                </div>
                                <small class="text-500">{{ wallet.description }}</small>
                            </div>
                        </div>

                        <div class="wallet-balance">
                            <div class="balance-amount">{{ formatCurrency(wallet.balance || 0) }}</div>
                            <div class="balance-meta">{{ wallet.currency || 'USD' }} · <span class="text-500">{{ wallet.transactionsCount }} tx</span></div>
                        </div>

                        <div class="wallet-footer">
                            <div class="footer-left">
                                <small class="text-500">Last activity</small>
                                <div class="last-activity">{{ formatDate(wallet.lastActivity) }}</div>
                            </div>

                            <div class="footer-actions">
                                <Button
                                    icon="pi pi-check"
                                    class="p-button-text p-button-success"
                                    :disabled="selectedWalletId === wallet.id"
                                    @click="setActiveWallet(wallet)"
                                />
                                <Button icon="pi pi-pencil" class="p-button-text" @click="openEdit(wallet)" />
                                <Button icon="pi pi-trash" class="p-button-text p-button-danger" @click="confirmDelete(wallet)" />
                            </div>
                        </div>
                    </div>

                    <div v-if="!loading && displayWallets.length === 0" class="empty-state">
                        <h6>No wallets yet</h6>
                        <p class="text-500">Create your first wallet to get started.</p>
                        <Button label="Create wallet" icon="pi pi-plus" class="p-button-sm" @click="openNew" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Dialog v-model:visible="walletDialog" :style="{ width: '450px' }" header="Wallet" modal class="p-fluid">
        <div class="field">
            <label for="name">Name</label>
            <InputText id="name" v-model.trim="walletForm.name" required autofocus />
            <small v-if="submitted && !walletForm.name" class="p-error">Name is required.</small>
        </div>

        <template #footer>
            <Button label="Cancel" icon="pi pi-times" class="p-button-text" @click="hideDialog" />
            <Button label="Save" icon="pi pi-check" class="p-button-text" @click="saveWallet" />
        </template>
    </Dialog>

    <Dialog v-model:visible="deleteDialog" :style="{ width: '450px' }" header="Confirm" modal>
        <div class="confirmation-content">
            <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
            <span v-if="walletToDelete">Are you sure you want to delete <b>{{ walletToDelete.name }}</b>?</span>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" class="p-button-text" @click="deleteDialog = false" />
            <Button label="Yes" icon="pi pi-check" class="p-button-text" @click="deleteWallet" />
        </template>
    </Dialog>

    <Toast />
</template>

<style scoped>
.wallet-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.wallet-card {
    background: var(--surface-card, #fff);
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 6px 16px rgba(16,24,40,0.06);
    border: 1px solid rgba(0,0,0,0.04);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 170px;
    transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
}

.wallet-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(16,24,40,0.08);
}

.wallet-card.active {
    border-color: rgba(34,197,94,0.7);
    box-shadow: 0 12px 36px rgba(34,197,94,0.06);
}

.wallet-header {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.avatar {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.05rem;
}

.wallet-title {
    flex: 1;
}

.name-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.wallet-name {
    font-weight: 700;
    font-size: 1rem;
}

.wallet-balance {
    margin-top: 1rem;
    display: flex;
    flex-direction: column;
}

.balance-amount {
    font-weight: 800;
    font-size: 1.25rem;
}

.balance-meta {
    color: rgba(0,0,0,0.6);
    font-size: 0.85rem;
}

.wallet-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 1rem;
}

.footer-left .last-activity {
    font-weight: 600;
}

.footer-actions .p-button {
    margin-left: 0.25rem;
}

.empty-state {
    grid-column: 1/-1;
    text-align: center;
    padding: 2rem 1rem;
}

.loading-overlay {
    grid-column: 1/-1;
    text-align: center;
    padding: 1rem 0;
    color: var(--text-color, #6b7280);
}

/* keep existing table rule in case other pages rely on it */
.wallets .p-datatable .p-datatable-thead > tr > th {
    white-space: nowrap;
}
</style>

EOF
)
