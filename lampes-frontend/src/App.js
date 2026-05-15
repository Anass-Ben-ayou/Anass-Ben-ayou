import React, { Suspense, lazy } from 'react'
import { Routes, Route } from 'react-router-dom'
import Navbar from './components/common/Navbar'
import Footer from './components/common/Footer'
import ProtectedRoute from './components/common/ProtectedRoute'
import AdminRoute from './components/common/AdminRoute'
import './App.css'

const Home = lazy(() => import('./components/pages/Home'))
const Boutique = lazy(() => import('./components/pages/Boutique'))
const Products = lazy(() => import('./components/pages/Products'))
const ProductDetails = lazy(() => import('./components/pages/ProductDetails'))
const Collections = lazy(() => import('./components/pages/Collections'))
const CollectionDetails = lazy(() => import('./components/pages/CollectionDetails'))
const About = lazy(() => import('./components/pages/About'))
const Contact = lazy(() => import('./components/pages/Contact'))
const Cart = lazy(() => import('./components/pages/Cart'))
const Checkout = lazy(() => import('./components/pages/Checkout'))
const Login = lazy(() => import('./components/pages/Login'))
const Register = lazy(() => import('./components/pages/Register'))
const ForgotPassword = lazy(() => import('./components/pages/ForgotPassword'))
const VerifyResetCode = lazy(() => import('./components/pages/VerifyResetCode'))
const ResetPassword = lazy(() => import('./components/pages/ResetPassword'))
const Dashboard = lazy(() => import('./components/pages/Dashboard'))
const AdminUsers = lazy(() => import('./components/pages/AdminUsers'))
const AdminProducts = lazy(() => import('./components/pages/AdminProducts'))
const AdminOrders = lazy(() => import('./components/pages/AdminOrders'))
const AdminPayments = lazy(() => import('./components/pages/AdminPayments'))
const PaymentSuccess = lazy(() => import('./components/pages/PaymentSuccess'))
const PaymentFailed = lazy(() => import('./components/pages/PaymentFailed'))
const PaymentPending = lazy(() => import('./components/pages/PaymentPending'))
const Orders = lazy(() => import('./components/pages/Orders'))

// Loads page components on demand so the first bundle stays smaller.
function App() {
  return (
    <div className="app">
      <Navbar />
      <main className="main-content">
        <Suspense fallback={<div className="container"><div className="loading-spinner"></div></div>}>
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/boutique" element={<Boutique />} />
            <Route path="/products" element={<Products />} />
            <Route path="/collections" element={<Collections />} />
            <Route path="/collections/:id" element={<CollectionDetails />} />
            <Route path="/a-propos" element={<About />} />
            <Route path="/contact" element={<Contact />} />
            <Route path="/product/:id" element={<ProductDetails />} />
            <Route path="/produit/:id" element={<ProductDetails />} />
            <Route
              path="/cart"
              element={(
                <ProtectedRoute>
                  <Cart />
                </ProtectedRoute>
              )}
            />
            <Route
              path="/checkout"
              element={(
                <ProtectedRoute>
                  <Checkout />
                </ProtectedRoute>
              )}
            />
            <Route path="/login" element={<Login />} />
            <Route path="/register" element={<Register />} />
            <Route path="/forgot-password" element={<ForgotPassword />} />
            <Route path="/verify-reset-code" element={<VerifyResetCode />} />
            <Route path="/reset-password" element={<ResetPassword />} />
            <Route
              path="/checkout/success"
              element={(
                <ProtectedRoute>
                  <PaymentSuccess />
                </ProtectedRoute>
              )}
            />
            <Route
              path="/checkout/failed"
              element={(
                <ProtectedRoute>
                  <PaymentFailed />
                </ProtectedRoute>
              )}
            />
            <Route
              path="/checkout/pending"
              element={(
                <ProtectedRoute>
                  <PaymentPending />
                </ProtectedRoute>
              )}
            />
            <Route
              path="/orders"
              element={(
                <ProtectedRoute>
                  <Orders />
                </ProtectedRoute>
              )}
            />
            <Route
              path="/dashboard"
              element={(
                <ProtectedRoute>
                  <Dashboard />
                </ProtectedRoute>
              )}
            />
            <Route
              path="/admin/users"
              element={(
                <AdminRoute>
                  <AdminUsers />
                </AdminRoute>
              )}
            />
            <Route
              path="/admin/products"
              element={(
                <AdminRoute>
                  <AdminProducts />
                </AdminRoute>
              )}
            />
            <Route
              path="/admin/orders"
              element={(
                <AdminRoute>
                  <AdminOrders />
                </AdminRoute>
              )}
            />
            <Route
              path="/admin/payments"
              element={(
                <AdminRoute>
                  <AdminPayments />
                </AdminRoute>
              )}
            />
          </Routes>
        </Suspense>
      </main>
      <Footer />
    </div>
  )
}

export default App
