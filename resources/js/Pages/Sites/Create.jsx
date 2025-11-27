import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Create({ auth }) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        domain: '',
        server_host: '',
        server_port: 22,
        server_user: '',
        db_name: '',
        db_user: '',
        db_password: '',
        http_port: 8080,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('sites.store'));
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    New WordPress Site
                </h2>
            }
        >
            <Head title="Create Site" />

            <div className="py-12">
                <div className="max-w-5xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="px-6 py-4 border-b flex items-center justify-between">
                            <h3 className="text-lg font-medium text-gray-900">
                                Create WordPress Site
                            </h3>
                            <Link
                                href={route('sites.index')}
                                className="text-sm text-indigo-600 hover:text-indigo-800"
                            >
                                Back to list
                            </Link>
                        </div>

                        <form onSubmit={submit} className="p-6 space-y-6">
                            {/* Site name + domain */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        Site Name
                                    </label>
                                    <input
                                        type="text"
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                    />
                                    {errors.name && (
                                        <p className="mt-1 text-sm text-red-600">{errors.name}</p>
                                    )}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        Domain
                                    </label>
                                    <input
                                        type="text"
                                        placeholder="example.com"
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        value={data.domain}
                                        onChange={(e) => setData('domain', e.target.value)}
                                    />
                                    {errors.domain && (
                                        <p className="mt-1 text-sm text-red-600">{errors.domain}</p>
                                    )}
                                </div>
                            </div>

                            {/* Server info */}
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        Server Host
                                    </label>
                                    <input
                                        type="text"
                                        placeholder="your-vps-ip-or-host"
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        value={data.server_host}
                                        onChange={(e) => setData('server_host', e.target.value)}
                                    />
                                    {errors.server_host && (
                                        <p className="mt-1 text-sm text-red-600">
                                            {errors.server_host}
                                        </p>
                                    )}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        SSH Port
                                    </label>
                                    <input
                                        type="number"
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        value={data.server_port}
                                        onChange={(e) => setData('server_port', e.target.value)}
                                    />
                                    {errors.server_port && (
                                        <p className="mt-1 text-sm text-red-600">
                                            {errors.server_port}
                                        </p>
                                    )}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        SSH User
                                    </label>
                                    <input
                                        type="text"
                                        placeholder="root"
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        value={data.server_user}
                                        onChange={(e) => setData('server_user', e.target.value)}
                                    />
                                    {errors.server_user && (
                                        <p className="mt-1 text-sm text-red-600">
                                            {errors.server_user}
                                        </p>
                                    )}
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        HTTP Port
                                    </label>
                                    <input
                                        type="number"
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        value={data.http_port}
                                        onChange={(e) => setData('http_port', e.target.value)}
                                    />
                                    {errors.http_port && (
                                        <p className="mt-1 text-sm text-red-600">
                                            {errors.http_port}
                                        </p>
                                    )}
                                    <p className="mt-1 text-xs text-gray-500">
                                        Each site should use a unique host port (e.g. 8080, 8081).
                                    </p>
                                </div>
                            </div>

                            {/* DB info */}
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        DB Name
                                    </label>
                                    <input
                                        type="text"
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        value={data.db_name}
                                        onChange={(e) => setData('db_name', e.target.value)}
                                    />
                                    {errors.db_name && (
                                        <p className="mt-1 text-sm text-red-600">{errors.db_name}</p>
                                    )}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        DB User
                                    </label>
                                    <input
                                        type="text"
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        value={data.db_user}
                                        onChange={(e) => setData('db_user', e.target.value)}
                                    />
                                    {errors.db_user && (
                                        <p className="mt-1 text-sm text-red-600">{errors.db_user}</p>
                                    )}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        DB Password
                                    </label>
                                    <input
                                        type="password"
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        value={data.db_password}
                                        onChange={(e) => setData('db_password', e.target.value)}
                                    />
                                    {errors.db_password && (
                                        <p className="mt-1 text-sm text-red-600">
                                            {errors.db_password}
                                        </p>
                                    )}
                                </div>
                            </div>

                            {/* Submit */}
                            <div className="flex items-center justify-end">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition"
                                >
                                    {processing ? 'Creating...' : 'Create Site'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}


