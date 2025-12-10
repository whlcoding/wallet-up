<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import ProductService from '@/service/ProductService';
import { useLayout } from '@/layout/composables/layout';
import {useWalletStore} from "@/stores/walletStore";
import walletTransactionsService from "@/service/WalletTransactionsService";

const useWallet = useWalletStore();


// exemplo de transações fictícias para exibir na UI (Income / Expense)
const walletTransactions = ref([
    { id: 1, wallet_id: 1, name: 'Salary - Acme Co.', type: 'Income', amount: 5200, status: 'Paid', date: '2025-12-01' },
    { id: 2, wallet_id: 1, name: 'Salary Bonus', type: 'Income', amount: 800, status: 'Paid', date: '2025-12-05' },
    { id: 3, wallet_id: 1, name: 'Rent', type: 'Expense', amount: 1200, status: 'Paid', date: '2025-12-03' },
    { id: 4, wallet_id: 1, name: 'Electricity Bill', type: 'Expense', amount: 95, status: 'Pending', date: '2025-12-09' },
    { id: 5, wallet_id: 1, name: 'Spotify Family', type: 'Expense', amount: 15, status: 'Paid', date: '2025-12-02' },
    { id: 6, wallet_id: 1, name: 'Freelance Project', type: 'Income', amount: 650, status: 'Paid', date: '2025-12-07' },
    { id: 7, wallet_id: 1, name: 'Gym Membership', type: 'Expense', amount: 45, status: 'Pending', date: '2025-12-10' }
]);

// invoke layout composable (no need to extract isDarkTheme here)
useLayout();

const products = ref(null);
const barData = reactive({
    labels: ['September', 'October', 'November', 'December'],
    datasets: [
        {
            label: 'Income',
            data: [5100, 5250, 5000, 5500],
            fill: false,
            backgroundColor: '#2f4860',
            borderColor: '#2f4860',
            tension: 0.4
        },
        {
            label: 'Expenses',
            data: [2800, 3100, 2500, 3000],
            fill: false,
            backgroundColor: '#00bb7e',
            borderColor: '#00bb7e',
            tension: 0.4
        }
    ]
});
const items = ref([
    { label: 'Add New', icon: 'pi pi-fw pi-plus' },
    { label: 'Remove', icon: 'pi pi-fw pi-minus' }
]);
const barOptions = ref(null);
const productService = new ProductService();

const accounts = ref([
    { name: 'Checking Account', number: 'Chase •••• 0929', balance: 8420.75 },
    { name: 'Savings Account', number: 'Ally •••• 5788', balance: 15460.33 }
]);

const goals = ref({
    title: 'Vacation Fund',
    target: 5000,
    progress: 46,
    nextCheck: '2026-01-01',
    note: 'Saving monthly to cover a 10-day trip next year.'
});

// novo: estilo dinâmico para o indicador circular de progresso
const goalProgressStyle = computed(() => ({
    background: `conic-gradient(var(--primary-500) 0 ${goals.value.progress}%, var(--surface-200) ${goals.value.progress}% 100%)`
}));

const upcomingBills = ref([
    { name: 'Electricity - City Power', date: 'Dec 09, 2025', amount: 95.00 },
    { name: 'Gym Membership', date: 'Dec 10, 2025', amount: 45.00 },
    { name: 'Spotify Family', date: 'Dec 15, 2025', amount: 14.99 }
]);

// contadores e métricas calculadas a partir das transações fictícias
const txCount = computed(() => (walletTransactions.value || []).length);
const monthlyIncome = computed(() => (walletTransactions.value || []).filter(t => t.type === 'Income').reduce((s, t) => s + Number(t.amount), 0));
const monthlyExpenses = computed(() => (walletTransactions.value || []).filter(t => t.type === 'Expense').reduce((s, t) => s + Number(t.amount), 0));
const savingsRate = computed(() => {
    const income = monthlyIncome.value;
    if (!income) return 0;
    const saved = income - monthlyExpenses.value;
    return Math.round((saved / income) * 100);
});

const totalBalance = computed(() => accounts.value.reduce((total, account) => total + account.balance, 0));

onMounted(() => {
    productService.getProductsSmall().then((data) => (products.value = data));
    // tenta popular via API se houver carteira selecionada, senão mantém os exemplos fictícios
    if (useWallet.selectedWallet) {
        walletTransactionsService.getTransactions(useWallet.selectedWallet.id).then((data) => {
            if (data && data.data && Array.isArray(data.data.data) && data.data.data.length) {
                walletTransactions.value = data.data.data;
            }
        }).catch(() => {
            // ignore: keep example data
        });
    }
});

const formatCurrency = (value) => {
    const number = Number(value);
    return number.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
};


const updateStatus = (transaction, status) => {
    // atualiza localmente (exemplo) e tenta enviar para a API
    transaction.status = status;
    walletTransactions.value = walletTransactions.value.map(t => (t.id === transaction.id ? transaction : t));
    walletTransactionsService.update(transaction.wallet_id, transaction.id, transaction).catch(() => {
        // se falhar, mantemos localmente e exibimos no console
        console.warn('Failed to update transaction status on server');
    });
};



</script>

<template>
    <div class="grid">

        <!-- Total Balance (melhor hierarquia, ações rápidas) -->
        <div class="col-12 xl:col-6">
            <div class="card h-full">
                <div class="flex align-items-start justify-content-between mb-4 gap-3">
                    <div>
                        <div class="flex align-items-center gap-3">
                            <p class="text-600 m-0">Total Balance</p>
                            <span class="surface-200 border-round px-2 py-1 text-600">{{ accounts.length }} accounts · {{ txCount }} txns</span>
                        </div>

                        <h2 class="text-900 m-0 mt-3" style="letter-spacing: -0.5px">{{ formatCurrency(totalBalance) }}</h2>
                        <p class="text-500 mt-2">Available balance across your linked accounts</p>
                    </div>

                    <div class="flex flex-column align-items-end gap-2">
                        <Button label="Add Money" icon="pi pi-plus" class="p-button-sm p-button-outlined" aria-label="Add money"></Button>
                        <Button label="Transfer" icon="pi pi-external-link" class="p-button-sm" aria-label="Transfer"></Button>
                    </div>
                </div>

                <div class="grid">
                    <div v-for="(account, index) in accounts" :key="index" class="col-12 md:col-6">
                        <div class="surface-50 border-1 surface-border border-round p-3">
                            <div class="flex align-items-center justify-content-between mb-2">
                                <div>
                                    <div class="text-700">{{ account.name }}</div>
                                    <div class="text-500 text-sm">{{ account.number }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-900 font-medium">{{ formatCurrency(account.balance) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex align-items-center justify-content-between mt-4">
                    <div>
                        <p class="text-600 mb-1">Goal Set</p>
                        <h4 class="text-900 m-0">{{ formatCurrency(goals.target) }}</h4>
                    </div>
                    <div class="flex align-items-center gap-2">
                        <i class="pi pi-chart-line text-green-500"></i>
                        <span class="text-600">{{ goals.note }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Goal card (mais foco no progresso + ação) -->
        <div class="col-12 md:col-6 xl:col-3">
            <div class="card h-full">
                <div class="flex align-items-center justify-content-between mb-3">
                    <div>
                        <p class="text-600 mb-1">{{ goals.title }}</p>
                        <h3 class="text-900 m-0">{{ formatCurrency(goals.target) }}</h3>
                    </div>
                    <div class="text-center">
                        <div class="goal-progress" :style="goalProgressStyle">{{ goals.progress }}%</div>
                        <p class="text-500 mt-2">Reached</p>
                    </div>
                </div>

                <div class="mb-3">
                    <p class="text-600 mb-1">Next check</p>
                    <h4 class="text-900 mt-0">{{ goals.nextCheck }}</h4>
                </div>

                <p class="text-600 m-0 mb-3">{{ goals.note }}</p>
                <div class="flex gap-2 mt-3">
                    <Button label="View Goal" icon="pi pi-eye" class="p-button-sm p-button-outlined"></Button>
                    <Button label="Contribute" icon="pi pi-wallet" class="p-button-sm p-button-success"></Button>
                </div>
            </div>
        </div>

        <!-- Upcoming Bills (melhor leitura, badges e ação) -->
        <div class="col-12 md:col-6 xl:col-3">
            <div class="card h-full">
                <div class="flex align-items-center justify-content-between mb-3">
                    <div>
                        <p class="text-600 mb-1">Upcoming Bills</p>
                        <h3 class="text-900 m-0">This week</h3>
                    </div>
                    <div class="surface-100 border-round p-2 text-center">
                        <p class="text-600 m-0">Next</p>
                        <h4 class="text-900 m-0">{{ formatCurrency(upcomingBills[0].amount) }}</h4>
                    </div>
                </div>

                <ul class="list-none p-0 m-0">
                    <li v-for="(bill, index) in upcomingBills" :key="index" class="flex align-items-center justify-content-between py-3 border-bottom-1 surface-border">
                        <div>
                            <p class="text-900 font-medium mb-1">{{ bill.name }}</p>
                            <div class="text-600 text-sm">{{ bill.date }}</div>
                        </div>
                        <div class="text-right">
                            <p class="text-900 font-semibold mb-1" :class="{'text-red-500': true}">{{ formatCurrency(bill.amount) }}</p>
                            <span class="text-500">to Pay</span>
                        </div>
                    </li>
                </ul>

                <div class="flex align-items-center justify-content-between mt-3">
                    <div class="text-500">Next bill: <strong>{{ upcomingBills[0].name }}</strong></div>
                    <div class="flex gap-2">
                        <Button label="Manage bills" icon="pi pi-cog" class="p-button-text p-button-sm"></Button>
                        <Button label="View all" icon="pi pi-list" class="p-button-sm p-button-outlined"></Button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 lg:col-6 d-flex">
            <div class="grid">
                <div class="col-12 lg:col-6">
                    <div class="card mb-0">
                        <div class="flex justify-content-between mb-3">
                            <div>
                                <span class="block text-500 font-medium mb-3">Monthly Income</span>
                                <div class="text-900 font-medium text-xl">{{ formatCurrency(monthlyIncome) }}</div>
                            </div>
                            <div class="flex align-items-center justify-content-center bg-green-100 border-round" style="width: 2.5rem; height: 2.5rem">
                                <i class="pi pi-wallet text-green-500 text-xl"></i>
                            </div>
                        </div>
                        <span class="text-500">Includes salary and freelance payments</span>
                    </div>
                </div>
                <div class="col-12 lg:col-6">
                    <div class="card mb-0">
                        <div class="flex justify-content-between mb-3">
                            <div>
                                <span class="block text-500 font-medium mb-3">Monthly Expenses</span>
                                <div class="text-900 font-medium text-xl">{{ formatCurrency(monthlyExpenses) }}</div>
                            </div>
                            <div class="flex align-items-center justify-content-center bg-red-100 border-round" style="width: 2.5rem; height: 2.5rem">
                                <i class="pi pi-dollar text-red-500 text-xl"></i>
                            </div>
                        </div>
                        <span class="text-500">Savings rate: <strong>{{ savingsRate }}%</strong></span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <h5>Recent Transactions</h5>
                        <DataTable :value="walletTransactions" :rows="5" :paginator="true" responsiveLayout="scroll">
                            <Column field="name" header="Name" :sortable="true" style="width: 35%"></Column>
                            <Column field="type" header="Type" :sortable="true" style="width: 15%">
                                <template #body="slotProps">
                                    <span class="p-column-title">Type</span>
                                    <span >{{ slotProps.data.type }}</span>
                                </template>
                            </Column>
                            <Column field="amount" header="Amount" :sortable="true" style="width: 35%">
                                <template #body="slotProps">
                                    <span :class="'transaction-price type-' + (slotProps.data.type ? slotProps.data.type.toLowerCase() : '')">{{ formatCurrency(slotProps.data.amount) }}</span>
                                </template>
                            </Column>
                            <Column field="status" header="Status" :sortable="true" style="width: 15%">
                                <template #body="slotProps">
                                    <span class="p-column-title">Status</span>
                                    <span :class="'transaction-badge status-' + (slotProps.data.status ? slotProps.data.status.toLowerCase() : '')">{{ slotProps.data.status }}</span>
                                </template>
                            </Column>
                            <Column style="width: 15%">
                                <template #header> Actions </template>
                                <template #body="slotProps">
                                    <div class="flex gap-1 justify-content-center">
                                        <Button icon="pi pi-thumbs-up" type="button" class="p-button-text" @click="updateStatus(slotProps.data, 'Paid')"></Button>
                                        <Button icon="pi pi-thumbs-down" type="button" class="p-button-text" @click="updateStatus(slotProps.data, 'Pending')"></Button>
                                    </div>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 xl:col-6">
            <div class="card">
                <h5>Overview</h5>
                <Chart type="bar" :data="barData" :options="barOptions" />
            </div>
            <div class="card">
                <div class="flex justify-content-between align-items-center mb-5">
                    <h5>Recurring Transactions</h5>
                    <div>
                        <Button icon="pi pi-ellipsis-v" class="p-button-text p-button-plain p-button-rounded" @click="$refs.menu2.toggle($event)"></Button>
                        <Menu ref="menu2" :popup="true" :model="items"></Menu>
                    </div>
                </div>
                <ul class="list-none p-0 m-0">
                    <li class="flex flex-column md:flex-row md:align-items-center md:justify-content-between mb-4">
                        <div>
                            <span class="text-900 font-medium mr-2 mb-1 md:mb-0">Space T-Shirt</span>
                            <div class="mt-1 text-600">Clothing</div>
                        </div>
                        <div class="mt-2 md:mt-0 flex align-items-center">
                            <div class="surface-300 border-round overflow-hidden w-10rem lg:w-6rem" style="height: 8px">
                                <div class="bg-orange-500 h-full" style="width: 50%"></div>
                            </div>
                            <span class="text-orange-500 ml-3 font-medium">%50</span>
                        </div>
                    </li>
                    <li class="flex flex-column md:flex-row md:align-items-center md:justify-content-between mb-4">
                        <div>
                            <span class="text-900 font-medium mr-2 mb-1 md:mb-0">Portal Sticker</span>
                            <div class="mt-1 text-600">Accessories</div>
                        </div>
                        <div class="mt-2 md:mt-0 ml-0 md:ml-8 flex align-items-center">
                            <div class="surface-300 border-round overflow-hidden w-10rem lg:w-6rem" style="height: 8px">
                                <div class="bg-cyan-500 h-full" style="width: 16%"></div>
                            </div>
                            <span class="text-cyan-500 ml-3 font-medium">%16</span>
                        </div>
                    </li>
                    <li class="flex flex-column md:flex-row md:align-items-center md:justify-content-between mb-4">
                        <div>
                            <span class="text-900 font-medium mr-2 mb-1 md:mb-0">Supernova Sticker</span>
                            <div class="mt-1 text-600">Accessories</div>
                        </div>
                        <div class="mt-2 md:mt-0 ml-0 md:ml-8 flex align-items-center">
                            <div class="surface-300 border-round overflow-hidden w-10rem lg:w-6rem" style="height: 8px">
                                <div class="bg-pink-500 h-full" style="width: 67%"></div>
                            </div>
                            <span class="text-pink-500 ml-3 font-medium">%67</span>
                        </div>
                    </li>
                    <li class="flex flex-column md:flex-row md:align-items-center md:justify-content-between mb-4">
                        <div>
                            <span class="text-900 font-medium mr-2 mb-1 md:mb-0">Wonders Notebook</span>
                            <div class="mt-1 text-600">Office</div>
                        </div>
                        <div class="mt-2 md:mt-0 ml-0 md:ml-8 flex align-items-center">
                            <div class="surface-300 border-round overflow-hidden w-10rem lg:w-6rem" style="height: 8px">
                                <div class="bg-green-500 h-full" style="width: 35%"></div>
                            </div>
                            <span class="text-green-500 ml-3 font-medium">%35</span>
                        </div>
                    </li>
                    <li class="flex flex-column md:flex-row md:align-items-center md:justify-content-between mb-4">
                        <div>
                            <span class="text-900 font-medium mr-2 mb-1 md:mb-0">Mat Black Case</span>
                            <div class="mt-1 text-600">Accessories</div>
                        </div>
                        <div class="mt-2 md:mt-0 ml-0 md:ml-8 flex align-items-center">
                            <div class="surface-300 border-round overflow-hidden w-10rem lg:w-6rem" style="height: 8px">
                                <div class="bg-purple-500 h-full" style="width: 75%"></div>
                            </div>
                            <span class="text-purple-500 ml-3 font-medium">%75</span>
                        </div>
                    </li>
                    <li class="flex flex-column md:flex-row md:align-items-center md:justify-content-between mb-4">
                        <div>
                            <span class="text-900 font-medium mr-2 mb-1 md:mb-0">Robots T-Shirt</span>
                            <div class="mt-1 text-600">Clothing</div>
                        </div>
                        <div class="mt-2 md:mt-0 ml-0 md:ml-8 flex align-items-center">
                            <div class="surface-300 border-round overflow-hidden w-10rem lg:w-6rem" style="height: 8px">
                                <div class="bg-teal-500 h-full" style="width: 40%"></div>
                            </div>
                            <span class="text-teal-500 ml-3 font-medium">%40</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-12 xl:col-6">
<!--            <div class="card">-->
<!--                <div class="flex align-items-center justify-content-between mb-4">-->
<!--                    <h5>Notifications</h5>-->
<!--                    <div>-->
<!--                        <Button icon="pi pi-ellipsis-v" class="p-button-text p-button-plain p-button-rounded" @click="$refs.menu1.toggle($event)"></Button>-->
<!--                        <Menu ref="menu1" :popup="true" :model="items"></Menu>-->
<!--                    </div>-->
<!--                </div>-->

<!--                <span class="block text-600 font-medium mb-3">TODAY</span>-->
<!--                <ul class="p-0 mx-0 mt-0 mb-4 list-none">-->
<!--                    <li class="flex align-items-center py-2 border-bottom-1 surface-border">-->
<!--                        <div class="w-3rem h-3rem flex align-items-center justify-content-center bg-blue-100 border-circle mr-3 flex-shrink-0">-->
<!--                            <i class="pi pi-dollar text-xl text-blue-500"></i>-->
<!--                        </div>-->
<!--                        <span class="text-900 line-height-3"-->
<!--                            >Richard Jones-->
<!--                            <span class="text-700">has purchased a blue t-shirt for <span class="text-blue-500">79$</span></span>-->
<!--                        </span>-->
<!--                    </li>-->
<!--                    <li class="flex align-items-center py-2">-->
<!--                        <div class="w-3rem h-3rem flex align-items-center justify-content-center bg-orange-100 border-circle mr-3 flex-shrink-0">-->
<!--                            <i class="pi pi-download text-xl text-orange-500"></i>-->
<!--                        </div>-->
<!--                        <span class="text-700 line-height-3">Your request for withdrawal of <span class="text-blue-500 font-medium">2500$</span> has been initiated.</span>-->
<!--                    </li>-->
<!--                </ul>-->

<!--                <span class="block text-600 font-medium mb-3">YESTERDAY</span>-->
<!--                <ul class="p-0 m-0 list-none">-->
<!--                    <li class="flex align-items-center py-2 border-bottom-1 surface-border">-->
<!--                        <div class="w-3rem h-3rem flex align-items-center justify-content-center bg-blue-100 border-circle mr-3 flex-shrink-0">-->
<!--                            <i class="pi pi-dollar text-xl text-blue-500"></i>-->
<!--                        </div>-->
<!--                        <span class="text-900 line-height-3"-->
<!--                            >Keyser Wick-->
<!--                            <span class="text-700">has purchased a black jacket for <span class="text-blue-500">59$</span></span>-->
<!--                        </span>-->
<!--                    </li>-->
<!--                    <li class="flex align-items-center py-2 border-bottom-1 surface-border">-->
<!--                        <div class="w-3rem h-3rem flex align-items-center justify-content-center bg-pink-100 border-circle mr-3 flex-shrink-0">-->
<!--                            <i class="pi pi-question text-xl text-pink-500"></i>-->
<!--                        </div>-->
<!--                        <span class="text-900 line-height-3"-->
<!--                            >Jane Davis-->
<!--                            <span class="text-700">has posted a new questions about your product.</span>-->
<!--                        </span>-->
<!--                    </li>-->
<!--                </ul>-->
<!--            </div>-->
        </div>
    </div>
</template>

<style scoped>
.goal-progress {
    width: 5rem;
    height: 5rem;
    border-radius: 50%;
    border: 8px solid var(--surface-100);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-700);
    font-weight: 700;
    font-size: 0.95rem;
}
</style>
